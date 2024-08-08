<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

use TypeError;
use ReflectionAttribute;
use ReflectionException;
use Staatic\Vendor\Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Staatic\Vendor\Symfony\Component\Config\Exception\LoaderLoadException;
use Staatic\Vendor\Symfony\Component\Config\FileLocatorInterface;
use Staatic\Vendor\Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;
use Staatic\Vendor\Symfony\Component\Config\Loader\Loader;
use Staatic\Vendor\Symfony\Component\Config\Resource\GlobResource;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\When;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler\RegisterAutoconfigureAttributesPass;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
abstract class FileLoader extends BaseFileLoader
{
    public const ANONYMOUS_ID_REGEXP = '/^\.\d+_[^~]*+~[._a-zA-Z\d]{7}$/';
    protected $container;
    protected $isLoadingInstanceof = \false;
    protected $instanceof = [];
    protected $interfaces = [];
    protected $singlyImplemented = [];
    protected $autoRegisterAliasesForSinglyImplementedInterfaces = \true;
    public function __construct(ContainerBuilder $container, FileLocatorInterface $locator, string $env = null)
    {
        $this->container = $container;
        parent::__construct($locator, $env);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @param bool|string $ignoreErrors
     * @param string|null $sourceResource
     * @return mixed
     */
    public function import($resource, $type = null, $ignoreErrors = \false, $sourceResource = null, $exclude = null)
    {
        $args = \func_get_args();
        if ($ignoreNotFound = 'not_found' === $ignoreErrors) {
            $args[2] = \false;
        } elseif (!\is_bool($ignoreErrors)) {
            throw new TypeError(sprintf('Invalid argument $ignoreErrors provided to "%s::import()": boolean or "not_found" expected, "%s" given.', static::class, get_debug_type($ignoreErrors)));
        }
        try {
            return parent::import(...$args);
        } catch (LoaderLoadException $e) {
            if (!$ignoreNotFound || !($prev = $e->getPrevious()) instanceof FileLocatorFileNotFoundException) {
                throw $e;
            }
            foreach ($prev->getTrace() as $frame) {
                if ('import' === ($frame['function'] ?? null) && is_a($frame['class'] ?? '', Loader::class, \true)) {
                    break;
                }
            }
            if (__FILE__ !== $frame['file']) {
                throw $e;
            }
        }
        return null;
    }
    /**
     * @param Definition $prototype
     * @param string $namespace
     * @param string $resource
     * @param string|mixed[]|null $exclude
     */
    public function registerClasses($prototype, $namespace, $resource, $exclude = null)
    {
        if (substr_compare($namespace, '\\', -strlen('\\')) !== 0) {
            throw new InvalidArgumentException(sprintf('Namespace prefix must end with a "\": "%s".', $namespace));
        }
        if (!preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+\\\\)++$/', $namespace)) {
            throw new InvalidArgumentException(sprintf('Namespace is not a valid PSR-4 prefix: "%s".', $namespace));
        }
        if (\is_array($exclude) && \in_array(null, $exclude, \true)) {
            throw new InvalidArgumentException('The exclude list must not contain a "null" value.');
        }
        if (\is_array($exclude) && \in_array('', $exclude, \true)) {
            throw new InvalidArgumentException('The exclude list must not contain an empty value.');
        }
        $source = (\func_num_args() > 4) ? func_get_arg(4) : null;
        $autoconfigureAttributes = new RegisterAutoconfigureAttributesPass();
        $autoconfigureAttributes = $autoconfigureAttributes->accept($prototype) ? $autoconfigureAttributes : null;
        $classes = $this->findClasses($namespace, $resource, (array) $exclude, $autoconfigureAttributes, $source);
        $serializedPrototype = serialize($prototype);
        foreach ($classes as $class => $errorMessage) {
            if (null === $errorMessage && $autoconfigureAttributes && $this->env) {
                $r = $this->container->getReflectionClass($class);
                $attribute = null;
                foreach ($r->getAttributes(When::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if ($this->env === $attribute->newInstance()->env) {
                        $attribute = null;
                        break;
                    }
                }
                if (null !== $attribute) {
                    continue;
                }
            }
            if (interface_exists($class, \false)) {
                $this->interfaces[] = $class;
            } else {
                $this->setDefinition($class, $definition = unserialize($serializedPrototype));
                if (null !== $errorMessage) {
                    $definition->addError($errorMessage);
                    continue;
                }
                $definition->setClass($class);
                foreach (class_implements($class, \false) as $interface) {
                    $this->singlyImplemented[$interface] = (($this->singlyImplemented[$interface] ?? $class) !== $class) ? \false : $class;
                }
            }
        }
        if ($this->autoRegisterAliasesForSinglyImplementedInterfaces) {
            $this->registerAliasesForSinglyImplementedInterfaces();
        }
    }
    public function registerAliasesForSinglyImplementedInterfaces()
    {
        foreach ($this->interfaces as $interface) {
            if (!empty($this->singlyImplemented[$interface]) && !$this->container->has($interface)) {
                $this->container->setAlias($interface, $this->singlyImplemented[$interface]);
            }
        }
        $this->interfaces = $this->singlyImplemented = [];
    }
    /**
     * @param string $id
     * @param Definition $definition
     */
    protected function setDefinition($id, $definition)
    {
        $this->container->removeBindings($id);
        if ($this->isLoadingInstanceof) {
            if (!$definition instanceof ChildDefinition) {
                throw new InvalidArgumentException(sprintf('Invalid type definition "%s": ChildDefinition expected, "%s" given.', $id, get_debug_type($definition)));
            }
            $this->instanceof[$id] = $definition;
        } else {
            $this->container->setDefinition($id, $definition->setInstanceofConditionals($this->instanceof));
        }
    }
    private function findClasses(string $namespace, string $pattern, array $excludePatterns, ?RegisterAutoconfigureAttributesPass $autoconfigureAttributes, ?string $source): array
    {
        $parameterBag = $this->container->getParameterBag();
        $excludePaths = [];
        $excludePrefix = null;
        $excludePatterns = $parameterBag->unescapeValue($parameterBag->resolveValue($excludePatterns));
        foreach ($excludePatterns as $excludePattern) {
            foreach ($this->glob($excludePattern, \true, $resource, \true, \true) as $path => $info) {
                $excludePrefix = $excludePrefix ?? $resource->getPrefix();
                $excludePaths[rtrim(str_replace('\\', '/', $path), '/')] = \true;
            }
        }
        $pattern = $parameterBag->unescapeValue($parameterBag->resolveValue($pattern));
        $classes = [];
        $prefixLen = null;
        foreach ($this->glob($pattern, \true, $resource, \false, \false, $excludePaths) as $path => $info) {
            if (null === $prefixLen) {
                $prefixLen = \strlen($resource->getPrefix());
                if ($excludePrefix && strncmp($excludePrefix, $resource->getPrefix(), strlen($resource->getPrefix())) !== 0) {
                    throw new InvalidArgumentException(sprintf('Invalid "exclude" pattern when importing classes for "%s": make sure your "exclude" pattern (%s) is a subset of the "resource" pattern (%s).', $namespace, $excludePattern, $pattern));
                }
            }
            if (isset($excludePaths[str_replace('\\', '/', $path)])) {
                continue;
            }
            if (substr_compare($path, '.php', -strlen('.php')) !== 0 || !$info->isReadable()) {
                continue;
            }
            $class = $namespace . ltrim(str_replace('/', '\\', substr($path, $prefixLen, -4)), '\\');
            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)*+$/', $class)) {
                continue;
            }
            try {
                $r = $this->container->getReflectionClass($class);
            } catch (ReflectionException $e) {
                $classes[$class] = $e->getMessage();
                continue;
            }
            if (!$r) {
                throw new InvalidArgumentException(sprintf('Expected to find class "%s" in file "%s" while importing services from resource "%s", but it was not found! Check the namespace prefix used with the resource.', $class, $path, $pattern));
            }
            if ($r->isInstantiable() || $r->isInterface()) {
                $classes[$class] = null;
            }
            if ($autoconfigureAttributes && !$r->isInstantiable()) {
                $autoconfigureAttributes->processClass($this->container, $r);
            }
        }
        if ($resource instanceof GlobResource) {
            $this->container->addResource($resource);
        } else {
            foreach ($resource as $path) {
                $this->container->fileExists($path, \false);
            }
        }
        if (null !== $prefixLen) {
            $attributes = (null !== $source) ? ['source' => sprintf('in "%s/%s"', basename(\dirname($source)), basename($source))] : [];
            foreach ($excludePaths as $path => $_) {
                $class = $namespace . ltrim(str_replace('/', '\\', substr($path, $prefixLen, substr_compare($path, '.php', -strlen('.php')) === 0 ? -4 : null)), '\\');
                if (!$this->container->has($class)) {
                    $this->container->register($class, $class)->setAbstract(\true)->addTag('container.excluded', $attributes);
                }
            }
        }
        return $classes;
    }
}
