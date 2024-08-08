<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use ReflectionClass;
use ReflectionException;
use Closure;
use Staatic\Vendor\Composer\InstalledVersions;
use Staatic\Vendor\Symfony\Component\Config\Resource\ClassExistenceResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\ComposerResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\DirectoryResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\FileExistenceResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\FileResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\GlobResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\ReflectionClassResource;
use Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\Target;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler\Compiler;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler\ResolveEnvPlaceholdersPass;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\InstantiatorInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\LazyServiceInstantiator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\RealServiceInstantiator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
class ContainerBuilder extends Container implements TaggedContainerInterface
{
    /**
     * @var mixed[]
     */
    private $extensions = [];
    /**
     * @var mixed[]
     */
    private $extensionsByNs = [];
    /**
     * @var mixed[]
     */
    private $definitions = [];
    /**
     * @var mixed[]
     */
    private $aliasDefinitions = [];
    /**
     * @var mixed[]
     */
    private $resources = [];
    /**
     * @var mixed[]
     */
    private $extensionConfigs = [];
    /**
     * @var Compiler
     */
    private $compiler;
    /**
     * @var bool
     */
    private $trackResources;
    /**
     * @var InstantiatorInterface
     */
    private $proxyInstantiator;
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;
    /**
     * @var mixed[]
     */
    private $expressionLanguageProviders = [];
    /**
     * @var mixed[]
     */
    private $usedTags = [];
    /**
     * @var mixed[]
     */
    private $envPlaceholders = [];
    /**
     * @var mixed[]
     */
    private $envCounters = [];
    /**
     * @var mixed[]
     */
    private $vendors;
    /**
     * @var mixed[]
     */
    private $autoconfiguredInstanceof = [];
    /**
     * @var mixed[]
     */
    private $autoconfiguredAttributes = [];
    /**
     * @var mixed[]
     */
    private $removedIds = [];
    /**
     * @var mixed[]
     */
    private $removedBindingIds = [];
    private const INTERNAL_TYPES = ['int' => \true, 'float' => \true, 'string' => \true, 'bool' => \true, 'resource' => \true, 'object' => \true, 'array' => \true, 'null' => \true, 'callable' => \true, 'iterable' => \true, 'mixed' => \true];
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
        $this->trackResources = interface_exists(ResourceInterface::class);
        $this->setDefinition('service_container', (new Definition(ContainerInterface::class))->setSynthetic(\true)->setPublic(\true));
    }
    /**
     * @var mixed[]
     */
    private $classReflectors;
    /**
     * @param bool $track
     */
    public function setResourceTracking($track)
    {
        $this->trackResources = $track;
    }
    public function isTrackingResources(): bool
    {
        return $this->trackResources;
    }
    /**
     * @param InstantiatorInterface $proxyInstantiator
     */
    public function setProxyInstantiator($proxyInstantiator)
    {
        $this->proxyInstantiator = $proxyInstantiator;
    }
    /**
     * @param ExtensionInterface $extension
     */
    public function registerExtension($extension)
    {
        $this->extensions[$extension->getAlias()] = $extension;
        if (\false !== $extension->getNamespace()) {
            $this->extensionsByNs[$extension->getNamespace()] = $extension;
        }
    }
    /**
     * @param string $name
     */
    public function getExtension($name): ExtensionInterface
    {
        if (isset($this->extensions[$name])) {
            return $this->extensions[$name];
        }
        if (isset($this->extensionsByNs[$name])) {
            return $this->extensionsByNs[$name];
        }
        throw new LogicException(sprintf('Container extension "%s" is not registered.', $name));
    }
    public function getExtensions(): array
    {
        return $this->extensions;
    }
    /**
     * @param string $name
     */
    public function hasExtension($name): bool
    {
        return isset($this->extensions[$name]) || isset($this->extensionsByNs[$name]);
    }
    public function getResources(): array
    {
        return array_values($this->resources);
    }
    /**
     * @param ResourceInterface $resource
     * @return static
     */
    public function addResource($resource)
    {
        if (!$this->trackResources) {
            return $this;
        }
        if ($resource instanceof GlobResource && $this->inVendors($resource->getPrefix())) {
            return $this;
        }
        $this->resources[(string) $resource] = $resource;
        return $this;
    }
    /**
     * @param mixed[] $resources
     * @return static
     */
    public function setResources($resources)
    {
        if (!$this->trackResources) {
            return $this;
        }
        $this->resources = $resources;
        return $this;
    }
    /**
     * @param object|string $object
     * @return static
     */
    public function addObjectResource($object)
    {
        if ($this->trackResources) {
            if (\is_object($object)) {
                $object = get_class($object);
            }
            if (!isset($this->classReflectors[$object])) {
                $this->classReflectors[$object] = new ReflectionClass($object);
            }
            $class = $this->classReflectors[$object];
            foreach ($class->getInterfaceNames() as $name) {
                if (null === $interface =& $this->classReflectors[$name]) {
                    $interface = new ReflectionClass($name);
                }
                $file = $interface->getFileName();
                if (\false !== $file && file_exists($file)) {
                    $this->fileExists($file);
                }
            }
            do {
                $file = $class->getFileName();
                if (\false !== $file && file_exists($file)) {
                    $this->fileExists($file);
                }
                foreach ($class->getTraitNames() as $name) {
                    $this->addObjectResource($name);
                }
            } while ($class = $class->getParentClass());
        }
        return $this;
    }
    /**
     * @param string|null $class
     * @param bool $throw
     */
    public function getReflectionClass($class, $throw = \true): ?ReflectionClass
    {
        if (!$class = $this->getParameterBag()->resolveValue($class)) {
            return null;
        }
        if (isset(self::INTERNAL_TYPES[$class])) {
            return null;
        }
        $resource = $classReflector = null;
        try {
            if (isset($this->classReflectors[$class])) {
                $classReflector = $this->classReflectors[$class];
            } elseif (class_exists(ClassExistenceResource::class)) {
                $resource = new ClassExistenceResource($class, \false);
                $classReflector = $resource->isFresh(0) ? \false : new ReflectionClass($class);
            } else {
                $classReflector = class_exists($class) ? new ReflectionClass($class) : \false;
            }
        } catch (ReflectionException $e) {
            if ($throw) {
                throw $e;
            }
        }
        if ($this->trackResources) {
            if (!$classReflector) {
                $this->addResource($resource ?? new ClassExistenceResource($class, \false));
            } elseif (!$classReflector->isInternal()) {
                $path = $classReflector->getFileName();
                if (!$this->inVendors($path)) {
                    $this->addResource(new ReflectionClassResource($classReflector, $this->vendors));
                }
            }
            $this->classReflectors[$class] = $classReflector;
        }
        return $classReflector ?: null;
    }
    /**
     * @param string $path
     * @param bool|string $trackContents
     */
    public function fileExists($path, $trackContents = \true): bool
    {
        $exists = file_exists($path);
        if (!$this->trackResources || $this->inVendors($path)) {
            return $exists;
        }
        if (!$exists) {
            $this->addResource(new FileExistenceResource($path));
            return $exists;
        }
        if (is_dir($path)) {
            if ($trackContents) {
                $this->addResource(new DirectoryResource($path, \is_string($trackContents) ? $trackContents : null));
            } else {
                $this->addResource(new GlobResource($path, '/*', \false));
            }
        } elseif ($trackContents) {
            $this->addResource(new FileResource($path));
        }
        return $exists;
    }
    /**
     * @param string $extension
     * @param mixed[]|null $values
     * @return static
     */
    public function loadFromExtension($extension, $values = null)
    {
        if ($this->isCompiled()) {
            throw new BadMethodCallException('Cannot load from an extension on a compiled container.');
        }
        $namespace = $this->getExtension($extension)->getAlias();
        $this->extensionConfigs[$namespace][] = $values ?? [];
        return $this;
    }
    /**
     * @param CompilerPassInterface $pass
     * @param string $type
     * @param int $priority
     * @return static
     */
    public function addCompilerPass($pass, $type = PassConfig::TYPE_BEFORE_OPTIMIZATION, $priority = 0)
    {
        $this->getCompiler()->addPass($pass, $type, $priority);
        $this->addObjectResource($pass);
        return $this;
    }
    public function getCompilerPassConfig(): PassConfig
    {
        return $this->getCompiler()->getPassConfig();
    }
    public function getCompiler(): Compiler
    {
        return $this->compiler = $this->compiler ?? new Compiler();
    }
    /**
     * @param string $id
     * @param object|null $service
     */
    public function set($id, $service)
    {
        if ($this->isCompiled() && (isset($this->definitions[$id]) && !$this->definitions[$id]->isSynthetic())) {
            throw new BadMethodCallException(sprintf('Setting service "%s" for an unknown or non-synthetic service definition on a compiled container is not allowed.', $id));
        }
        unset($this->definitions[$id], $this->aliasDefinitions[$id], $this->removedIds[$id]);
        parent::set($id, $service);
    }
    /**
     * @param string $id
     */
    public function removeDefinition($id)
    {
        if (isset($this->definitions[$id])) {
            unset($this->definitions[$id]);
            $this->removedIds[$id] = \true;
        }
    }
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return isset($this->definitions[$id]) || isset($this->aliasDefinitions[$id]) || parent::has($id);
    }
    /**
     * @param string $id
     * @param int $invalidBehavior
     */
    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if ($this->isCompiled() && isset($this->removedIds[$id])) {
            return (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE >= $invalidBehavior) ? parent::get($id) : null;
        }
        return $this->doGet($id, $invalidBehavior);
    }
    /**
     * @return mixed
     */
    private function doGet(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, array &$inlineServices = null, bool $isConstructorArgument = \false)
    {
        if (isset($inlineServices[$id])) {
            return $inlineServices[$id];
        }
        if (null === $inlineServices) {
            $isConstructorArgument = \true;
            $inlineServices = [];
        }
        try {
            if (ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE === $invalidBehavior) {
                return $this->privates[$id] ?? parent::get($id, $invalidBehavior);
            }
            if (null !== $service = $this->privates[$id] ?? parent::get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE)) {
                return $service;
            }
        } catch (ServiceCircularReferenceException $e) {
            if ($isConstructorArgument) {
                throw $e;
            }
        }
        if (!isset($this->definitions[$id]) && isset($this->aliasDefinitions[$id])) {
            $alias = $this->aliasDefinitions[$id];
            if ($alias->isDeprecated()) {
                $deprecation = $alias->getDeprecation($id);
                trigger_deprecation($deprecation['package'], $deprecation['version'], $deprecation['message']);
            }
            return $this->doGet((string) $alias, $invalidBehavior, $inlineServices, $isConstructorArgument);
        }
        try {
            $definition = $this->getDefinition($id);
        } catch (ServiceNotFoundException $e) {
            if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE < $invalidBehavior) {
                return null;
            }
            throw $e;
        }
        if ($definition->hasErrors() && $e = $definition->getErrors()) {
            throw new RuntimeException(reset($e));
        }
        if ($isConstructorArgument) {
            $this->loading[$id] = \true;
        }
        try {
            return $this->createService($definition, $inlineServices, $isConstructorArgument, $id);
        } finally {
            if ($isConstructorArgument) {
                unset($this->loading[$id]);
            }
        }
    }
    /**
     * @param $this $container
     */
    public function merge($container)
    {
        if ($this->isCompiled()) {
            throw new BadMethodCallException('Cannot merge on a compiled container.');
        }
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!$definition->hasTag('container.excluded') || !$this->has($id)) {
                $this->setDefinition($id, $definition);
            }
        }
        $this->addAliases($container->getAliases());
        $this->getParameterBag()->add($container->getParameterBag()->all());
        if ($this->trackResources) {
            foreach ($container->getResources() as $resource) {
                $this->addResource($resource);
            }
        }
        foreach ($this->extensions as $name => $extension) {
            if (!isset($this->extensionConfigs[$name])) {
                $this->extensionConfigs[$name] = [];
            }
            $this->extensionConfigs[$name] = array_merge($this->extensionConfigs[$name], $container->getExtensionConfig($name));
        }
        if ($this->getParameterBag() instanceof EnvPlaceholderParameterBag && $container->getParameterBag() instanceof EnvPlaceholderParameterBag) {
            $envPlaceholders = $container->getParameterBag()->getEnvPlaceholders();
            $this->getParameterBag()->mergeEnvPlaceholders($container->getParameterBag());
        } else {
            $envPlaceholders = [];
        }
        foreach ($container->envCounters as $env => $count) {
            if (!$count && !isset($envPlaceholders[$env])) {
                continue;
            }
            if (!isset($this->envCounters[$env])) {
                $this->envCounters[$env] = $count;
            } else {
                $this->envCounters[$env] += $count;
            }
        }
        foreach ($container->getAutoconfiguredInstanceof() as $interface => $childDefinition) {
            if (isset($this->autoconfiguredInstanceof[$interface])) {
                throw new InvalidArgumentException(sprintf('"%s" has already been autoconfigured and merge() does not support merging autoconfiguration for the same class/interface.', $interface));
            }
            $this->autoconfiguredInstanceof[$interface] = $childDefinition;
        }
        foreach ($container->getAutoconfiguredAttributes() as $attribute => $configurator) {
            if (isset($this->autoconfiguredAttributes[$attribute])) {
                throw new InvalidArgumentException(sprintf('"%s" has already been autoconfigured and merge() does not support merging autoconfiguration for the same attribute.', $attribute));
            }
            $this->autoconfiguredAttributes[$attribute] = $configurator;
        }
    }
    /**
     * @param string $name
     */
    public function getExtensionConfig($name): array
    {
        if (!isset($this->extensionConfigs[$name])) {
            $this->extensionConfigs[$name] = [];
        }
        return $this->extensionConfigs[$name];
    }
    /**
     * @param string $name
     * @param mixed[] $config
     */
    public function prependExtensionConfig($name, $config)
    {
        if (!isset($this->extensionConfigs[$name])) {
            $this->extensionConfigs[$name] = [];
        }
        array_unshift($this->extensionConfigs[$name], $config);
    }
    /**
     * @param bool $resolveEnvPlaceholders
     */
    public function compile($resolveEnvPlaceholders = \false)
    {
        $compiler = $this->getCompiler();
        if ($this->trackResources) {
            foreach ($compiler->getPassConfig()->getPasses() as $pass) {
                $this->addObjectResource($pass);
            }
        }
        $bag = $this->getParameterBag();
        if ($resolveEnvPlaceholders && $bag instanceof EnvPlaceholderParameterBag) {
            $compiler->addPass(new ResolveEnvPlaceholdersPass(), PassConfig::TYPE_AFTER_REMOVING, -1000);
        }
        $compiler->compile($this);
        foreach ($this->definitions as $id => $definition) {
            if ($this->trackResources && $definition->isLazy()) {
                $this->getReflectionClass($definition->getClass());
            }
        }
        $this->extensionConfigs = [];
        if ($bag instanceof EnvPlaceholderParameterBag) {
            if ($resolveEnvPlaceholders) {
                $this->parameterBag = new ParameterBag($this->resolveEnvPlaceholders($bag->all(), \true));
            }
            $this->envPlaceholders = $bag->getEnvPlaceholders();
        }
        parent::compile();
        foreach ($this->definitions + $this->aliasDefinitions as $id => $definition) {
            if (!$definition->isPublic() || $definition->isPrivate()) {
                $this->removedIds[$id] = \true;
            }
        }
    }
    public function getServiceIds(): array
    {
        return array_map('strval', array_unique(array_merge(array_keys($this->getDefinitions()), array_keys($this->aliasDefinitions), parent::getServiceIds())));
    }
    public function getRemovedIds(): array
    {
        return $this->removedIds;
    }
    /**
     * @param mixed[] $aliases
     */
    public function addAliases($aliases)
    {
        foreach ($aliases as $alias => $id) {
            $this->setAlias($alias, $id);
        }
    }
    /**
     * @param mixed[] $aliases
     */
    public function setAliases($aliases)
    {
        $this->aliasDefinitions = [];
        $this->addAliases($aliases);
    }
    /**
     * @param string $alias
     * @param string|Alias $id
     */
    public function setAlias($alias, $id): Alias
    {
        if ('' === $alias || '\\' === $alias[-1] || \strlen($alias) !== strcspn($alias, "\x00\r\n'")) {
            throw new InvalidArgumentException(sprintf('Invalid alias id: "%s".', $alias));
        }
        if (\is_string($id)) {
            $id = new Alias($id);
        }
        if ($alias === (string) $id) {
            throw new InvalidArgumentException(sprintf('An alias cannot reference itself, got a circular reference on "%s".', $alias));
        }
        unset($this->definitions[$alias], $this->removedIds[$alias]);
        return $this->aliasDefinitions[$alias] = $id;
    }
    /**
     * @param string $alias
     */
    public function removeAlias($alias)
    {
        if (isset($this->aliasDefinitions[$alias])) {
            unset($this->aliasDefinitions[$alias]);
            $this->removedIds[$alias] = \true;
        }
    }
    /**
     * @param string $id
     */
    public function hasAlias($id): bool
    {
        return isset($this->aliasDefinitions[$id]);
    }
    public function getAliases(): array
    {
        return $this->aliasDefinitions;
    }
    /**
     * @param string $id
     */
    public function getAlias($id): Alias
    {
        if (!isset($this->aliasDefinitions[$id])) {
            throw new InvalidArgumentException(sprintf('The service alias "%s" does not exist.', $id));
        }
        return $this->aliasDefinitions[$id];
    }
    /**
     * @param string $id
     * @param string|null $class
     */
    public function register($id, $class = null): Definition
    {
        return $this->setDefinition($id, new Definition($class));
    }
    /**
     * @param string $id
     * @param string|null $class
     */
    public function autowire($id, $class = null): Definition
    {
        return $this->setDefinition($id, (new Definition($class))->setAutowired(\true));
    }
    /**
     * @param mixed[] $definitions
     */
    public function addDefinitions($definitions)
    {
        foreach ($definitions as $id => $definition) {
            $this->setDefinition($id, $definition);
        }
    }
    /**
     * @param mixed[] $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = [];
        $this->addDefinitions($definitions);
    }
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
    /**
     * @param string $id
     * @param Definition $definition
     */
    public function setDefinition($id, $definition): Definition
    {
        if ($this->isCompiled()) {
            throw new BadMethodCallException('Adding definition to a compiled container is not allowed.');
        }
        if ('' === $id || '\\' === $id[-1] || \strlen($id) !== strcspn($id, "\x00\r\n'")) {
            throw new InvalidArgumentException(sprintf('Invalid service id: "%s".', $id));
        }
        unset($this->aliasDefinitions[$id], $this->removedIds[$id]);
        return $this->definitions[$id] = $definition;
    }
    /**
     * @param string $id
     */
    public function hasDefinition($id): bool
    {
        return isset($this->definitions[$id]);
    }
    /**
     * @param string $id
     */
    public function getDefinition($id): Definition
    {
        if (!isset($this->definitions[$id])) {
            throw new ServiceNotFoundException($id);
        }
        return $this->definitions[$id];
    }
    /**
     * @param string $id
     */
    public function findDefinition($id): Definition
    {
        $seen = [];
        while (isset($this->aliasDefinitions[$id])) {
            $id = (string) $this->aliasDefinitions[$id];
            if (isset($seen[$id])) {
                $seen = array_values($seen);
                $seen = \array_slice($seen, array_search($id, $seen));
                $seen[] = $id;
                throw new ServiceCircularReferenceException($id, $seen);
            }
            $seen[$id] = $id;
        }
        return $this->getDefinition($id);
    }
    /**
     * @param bool|object $tryProxy
     * @return mixed
     */
    private function createService(Definition $definition, array &$inlineServices, bool $isConstructorArgument = \false, string $id = null, $tryProxy = \true)
    {
        if (null === $id && isset($inlineServices[$h = spl_object_hash($definition)])) {
            return $inlineServices[$h];
        }
        if ($definition instanceof ChildDefinition) {
            throw new RuntimeException(sprintf('Constructing service "%s" from a parent definition is not supported at build time.', $id));
        }
        if ($definition->isSynthetic()) {
            throw new RuntimeException(sprintf('You have requested a synthetic service ("%s"). The DIC does not know how to construct this service.', $id));
        }
        if ($definition->isDeprecated()) {
            $deprecation = $definition->getDeprecation($id);
            trigger_deprecation($deprecation['package'], $deprecation['version'], $deprecation['message']);
        }
        $parameterBag = $this->getParameterBag();
        if (\true === $tryProxy && $definition->isLazy() && !$tryProxy = !($proxy = $this->proxyInstantiator = $this->proxyInstantiator ?? new LazyServiceInstantiator()) || $proxy instanceof RealServiceInstantiator) {
            $proxy = $proxy->instantiateProxy($this, (clone $definition)->setClass($parameterBag->resolveValue($definition->getClass()))->setTags(($definition->hasTag('proxy') ? ['proxy' => $parameterBag->resolveValue($definition->getTag('proxy'))] : []) + $definition->getTags()), $id, function ($proxy = \false) use ($definition, &$inlineServices, $id) {
                return $this->createService($definition, $inlineServices, \true, $id, $proxy);
            });
            $this->shareService($definition, $proxy, $id, $inlineServices);
            return $proxy;
        }
        if (null !== $definition->getFile()) {
            require_once $parameterBag->resolveValue($definition->getFile());
        }
        $arguments = $definition->getArguments();
        if (null !== $factory = $definition->getFactory()) {
            if (\is_array($factory)) {
                $factory = [$this->doResolveServices($parameterBag->resolveValue($factory[0]), $inlineServices, $isConstructorArgument), $factory[1]];
            } elseif (!\is_string($factory)) {
                throw new RuntimeException(sprintf('Cannot create service "%s" because of invalid factory.', $id));
            } elseif (strncmp($factory, '@=', strlen('@=')) === 0) {
                $factory = function (ServiceLocator $arguments) use ($factory) {
                    return $this->getExpressionLanguage()->evaluate(substr($factory, 2), ['container' => $this, 'args' => $arguments]);
                };
                $arguments = [new ServiceLocatorArgument($arguments)];
            }
        }
        $arguments = $this->doResolveServices($parameterBag->unescapeValue($parameterBag->resolveValue($arguments)), $inlineServices, $isConstructorArgument);
        if (null !== $id && $definition->isShared() && (isset($this->services[$id]) || isset($this->privates[$id])) && (\true === $tryProxy || !$definition->isLazy())) {
            return $this->services[$id] ?? $this->privates[$id];
        }
        $arrayIsListFunction = function (array $array) : bool {
            if (function_exists('array_is_list')) {
                return array_is_list($array);
            }
            if ($array === []) {
                return true;
            }
            $current_key = 0;
            foreach ($array as $key => $noop) {
                if ($key !== $current_key) {
                    return false;
                }
                ++$current_key;
            }
            return true;
        };
        if (!$arrayIsListFunction($arguments)) {
            $arguments = array_combine(array_map(function ($k) {
                return preg_replace('/^.*\$/', '', $k);
            }, array_keys($arguments)), $arguments);
        }
        if (null !== $factory) {
            $service = $factory(...$arguments);
            if (!$definition->isDeprecated() && \is_array($factory) && \is_string($factory[0])) {
                $r = new ReflectionClass($factory[0]);
                if (0 < strpos($r->getDocComment(), "\n * @deprecated ")) {
                    trigger_deprecation('', '', 'The "%s" service relies on the deprecated "%s" factory class. It should either be deprecated or its factory upgraded.', $id, $r->name);
                }
            }
        } else {
            $r = new ReflectionClass($parameterBag->resolveValue($definition->getClass()));
            if (\is_object($tryProxy)) {
                if ($r->getConstructor()) {
                    $tryProxy->__construct(...$arguments);
                }
                $service = $tryProxy;
            } else {
                $service = $r->getConstructor() ? $r->newInstanceArgs($arguments) : $r->newInstance();
            }
            if (!$definition->isDeprecated() && 0 < strpos($r->getDocComment(), "\n * @deprecated ")) {
                trigger_deprecation('', '', 'The "%s" service relies on the deprecated "%s" class. It should either be deprecated or its implementation upgraded.', $id, $r->name);
            }
        }
        $lastWitherIndex = null;
        foreach ($definition->getMethodCalls() as $k => $call) {
            if ($call[2] ?? \false) {
                $lastWitherIndex = $k;
            }
        }
        if (null === $lastWitherIndex && (\true === $tryProxy || !$definition->isLazy())) {
            $this->shareService($definition, $service, $id, $inlineServices);
        }
        $properties = $this->doResolveServices($parameterBag->unescapeValue($parameterBag->resolveValue($definition->getProperties())), $inlineServices);
        foreach ($properties as $name => $value) {
            $service->{$name} = $value;
        }
        foreach ($definition->getMethodCalls() as $k => $call) {
            $service = $this->callMethod($service, $call, $inlineServices);
            if ($lastWitherIndex === $k && (\true === $tryProxy || !$definition->isLazy())) {
                $this->shareService($definition, $service, $id, $inlineServices);
            }
        }
        if ($callable = $definition->getConfigurator()) {
            if (\is_array($callable)) {
                $callable[0] = $parameterBag->resolveValue($callable[0]);
                if ($callable[0] instanceof Reference) {
                    $callable[0] = $this->doGet((string) $callable[0], $callable[0]->getInvalidBehavior(), $inlineServices);
                } elseif ($callable[0] instanceof Definition) {
                    $callable[0] = $this->createService($callable[0], $inlineServices);
                }
            }
            if (!\is_callable($callable)) {
                throw new InvalidArgumentException(sprintf('The configure callable for class "%s" is not a callable.', get_debug_type($service)));
            }
            $callable($service);
        }
        return $service;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolveServices($value)
    {
        return $this->doResolveServices($value);
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    private function doResolveServices($value, array &$inlineServices = [], bool $isConstructorArgument = \false)
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doResolveServices($v, $inlineServices, $isConstructorArgument);
            }
        } elseif ($value instanceof ServiceClosureArgument) {
            $reference = $value->getValues()[0];
            $value = function () use ($reference) {
                return $this->resolveServices($reference);
            };
        } elseif ($value instanceof IteratorArgument) {
            $value = new RewindableGenerator(function () use ($value, &$inlineServices) {
                foreach ($value->getValues() as $k => $v) {
                    foreach (self::getServiceConditionals($v) as $s) {
                        if (!$this->has($s)) {
                            continue 2;
                        }
                    }
                    foreach (self::getInitializedConditionals($v) as $s) {
                        if (!$this->doGet($s, ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE, $inlineServices)) {
                            continue 2;
                        }
                    }
                    yield $k => $this->doResolveServices($v, $inlineServices);
                }
            }, function () use ($value): int {
                $count = 0;
                foreach ($value->getValues() as $v) {
                    foreach (self::getServiceConditionals($v) as $s) {
                        if (!$this->has($s)) {
                            continue 2;
                        }
                    }
                    foreach (self::getInitializedConditionals($v) as $s) {
                        if (!$this->doGet($s, ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE)) {
                            continue 2;
                        }
                    }
                    ++$count;
                }
                return $count;
            });
        } elseif ($value instanceof ServiceLocatorArgument) {
            $refs = $types = [];
            foreach ($value->getValues() as $k => $v) {
                $refs[$k] = [$v, null];
                $types[$k] = ($v instanceof TypedReference) ? $v->getType() : '?';
            }
            $value = new ServiceLocator(Closure::fromCallable([$this, 'resolveServices']), $refs, $types);
        } elseif ($value instanceof Reference) {
            $value = $this->doGet((string) $value, $value->getInvalidBehavior(), $inlineServices, $isConstructorArgument);
        } elseif ($value instanceof Definition) {
            $value = $this->createService($value, $inlineServices, $isConstructorArgument);
        } elseif ($value instanceof Parameter) {
            $value = $this->getParameter((string) $value);
        } elseif ($value instanceof Expression) {
            $value = $this->getExpressionLanguage()->evaluate($value, ['container' => $this]);
        } elseif ($value instanceof AbstractArgument) {
            throw new RuntimeException($value->getTextWithContext());
        }
        return $value;
    }
    /**
     * @param string $name
     * @param bool $throwOnAbstract
     */
    public function findTaggedServiceIds($name, $throwOnAbstract = \false): array
    {
        $this->usedTags[] = $name;
        $tags = [];
        foreach ($this->getDefinitions() as $id => $definition) {
            if ($definition->hasTag($name) && !$definition->hasTag('container.excluded')) {
                if ($throwOnAbstract && $definition->isAbstract()) {
                    throw new InvalidArgumentException(sprintf('The service "%s" tagged "%s" must not be abstract.', $id, $name));
                }
                $tags[$id] = $definition->getTag($name);
            }
        }
        return $tags;
    }
    public function findTags(): array
    {
        $tags = [];
        foreach ($this->getDefinitions() as $id => $definition) {
            $tags[] = array_keys($definition->getTags());
        }
        return array_unique(array_merge([], ...$tags));
    }
    public function findUnusedTags(): array
    {
        return array_values(array_diff($this->findTags(), $this->usedTags));
    }
    /**
     * @param ExpressionFunctionProviderInterface $provider
     */
    public function addExpressionLanguageProvider($provider)
    {
        $this->expressionLanguageProviders[] = $provider;
    }
    public function getExpressionLanguageProviders(): array
    {
        return $this->expressionLanguageProviders;
    }
    /**
     * @param string $interface
     */
    public function registerForAutoconfiguration($interface): ChildDefinition
    {
        if (!isset($this->autoconfiguredInstanceof[$interface])) {
            $this->autoconfiguredInstanceof[$interface] = new ChildDefinition('');
        }
        return $this->autoconfiguredInstanceof[$interface];
    }
    /**
     * @param string $attributeClass
     * @param callable $configurator
     */
    public function registerAttributeForAutoconfiguration($attributeClass, $configurator): void
    {
        $this->autoconfiguredAttributes[$attributeClass] = $configurator;
    }
    /**
     * @param string $id
     * @param string $type
     * @param string|null $name
     */
    public function registerAliasForArgument($id, $type, $name = null): Alias
    {
        $name = (new Target($name ?? $id))->name;
        if (!preg_match('/^[a-zA-Z_\x7f-\xff]/', $name)) {
            throw new InvalidArgumentException(sprintf('Invalid argument name "%s" for service "%s": the first character must be a letter.', $name, $id));
        }
        return $this->setAlias($type . ' $' . $name, $id);
    }
    public function getAutoconfiguredInstanceof(): array
    {
        return $this->autoconfiguredInstanceof;
    }
    public function getAutoconfiguredAttributes(): array
    {
        return $this->autoconfiguredAttributes;
    }
    /**
     * @param mixed $value
     * @param string|bool|null $format
     * @param mixed[]|null $usedEnvs
     * @return mixed
     */
    public function resolveEnvPlaceholders($value, $format = null, &$usedEnvs = null)
    {
        $bag = $this->getParameterBag();
        if (\true === ($format = $format ?? '%%env(%s)%%')) {
            $value = $bag->resolveValue($value);
        }
        if ($value instanceof Definition) {
            $value = (array) $value;
        }
        if (\is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[\is_string($k) ? $this->resolveEnvPlaceholders($k, $format, $usedEnvs) : $k] = $this->resolveEnvPlaceholders($v, $format, $usedEnvs);
            }
            return $result;
        }
        if (!\is_string($value) || 38 > \strlen($value) || \false === stripos($value, 'env_')) {
            return $value;
        }
        $envPlaceholders = ($bag instanceof EnvPlaceholderParameterBag) ? $bag->getEnvPlaceholders() : $this->envPlaceholders;
        $completed = \false;
        preg_match_all('/env_[a-f0-9]{16}_\w+_[a-f0-9]{32}/Ui', $value, $matches);
        $usedPlaceholders = array_flip($matches[0]);
        foreach ($envPlaceholders as $env => $placeholders) {
            foreach ($placeholders as $placeholder) {
                if (isset($usedPlaceholders[$placeholder])) {
                    if (\true === $format) {
                        $resolved = $bag->escapeValue($this->getEnv($env));
                    } else {
                        $resolved = sprintf($format, $env);
                    }
                    if ($placeholder === $value) {
                        $value = $resolved;
                        $completed = \true;
                    } else {
                        if (!\is_string($resolved) && !is_numeric($resolved)) {
                            throw new RuntimeException(sprintf('A string value must be composed of strings and/or numbers, but found parameter "env(%s)" of type "%s" inside string value "%s".', $env, get_debug_type($resolved), $this->resolveEnvPlaceholders($value)));
                        }
                        $value = str_ireplace($placeholder, $resolved, $value);
                    }
                    $usedEnvs[$env] = $env;
                    $this->envCounters[$env] = isset($this->envCounters[$env]) ? 1 + $this->envCounters[$env] : 1;
                    if ($completed) {
                        break 2;
                    }
                }
            }
        }
        return $value;
    }
    public function getEnvCounters(): array
    {
        $bag = $this->getParameterBag();
        $envPlaceholders = ($bag instanceof EnvPlaceholderParameterBag) ? $bag->getEnvPlaceholders() : $this->envPlaceholders;
        foreach ($envPlaceholders as $env => $placeholders) {
            if (!isset($this->envCounters[$env])) {
                $this->envCounters[$env] = 0;
            }
        }
        return $this->envCounters;
    }
    /**
     * @param CompilerPassInterface $pass
     * @param string $message
     */
    public function log($pass, $message)
    {
        $this->getCompiler()->log($pass, $this->resolveEnvPlaceholders($message));
    }
    /**
     * @param string $package
     * @param string $class
     * @param mixed[] $parentPackages
     */
    final public static function willBeAvailable($package, $class, $parentPackages): bool
    {
        if (!class_exists(InstalledVersions::class)) {
            throw new \LogicException(sprintf('Calling "%s" when dependencies have been installed with Composer 1 is not supported. Consider upgrading to Composer 2.', __METHOD__));
        }
        if (!class_exists($class) && !interface_exists($class, \false) && !trait_exists($class, \false)) {
            return \false;
        }
        if (!InstalledVersions::isInstalled($package) || InstalledVersions::isInstalled($package, \false)) {
            return \true;
        }
        $rootPackage = InstalledVersions::getRootPackage()['name'] ?? '';
        if ('symfony/symfony' === $rootPackage) {
            return \true;
        }
        foreach ($parentPackages as $parentPackage) {
            if ($rootPackage === $parentPackage || InstalledVersions::isInstalled($parentPackage) && !InstalledVersions::isInstalled($parentPackage, \false)) {
                return \true;
            }
        }
        return \false;
    }
    public function getRemovedBindingIds(): array
    {
        return $this->removedBindingIds;
    }
    /**
     * @param string $id
     */
    public function removeBindings($id)
    {
        if ($this->hasDefinition($id)) {
            foreach ($this->getDefinition($id)->getBindings() as $key => $binding) {
                [, $bindingId] = $binding->getValues();
                $this->removedBindingIds[(int) $bindingId] = \true;
            }
        }
    }
    /**
     * @param mixed $value
     */
    public static function getServiceConditionals($value): array
    {
        $services = [];
        if (\is_array($value)) {
            foreach ($value as $v) {
                $services = array_unique(array_merge($services, self::getServiceConditionals($v)));
            }
        } elseif ($value instanceof Reference && ContainerInterface::IGNORE_ON_INVALID_REFERENCE === $value->getInvalidBehavior()) {
            $services[] = (string) $value;
        }
        return $services;
    }
    /**
     * @param mixed $value
     */
    public static function getInitializedConditionals($value): array
    {
        $services = [];
        if (\is_array($value)) {
            foreach ($value as $v) {
                $services = array_unique(array_merge($services, self::getInitializedConditionals($v)));
            }
        } elseif ($value instanceof Reference && ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE === $value->getInvalidBehavior()) {
            $services[] = (string) $value;
        }
        return $services;
    }
    /**
     * @param mixed $value
     */
    public static function hash($value): string
    {
        $hash = substr(base64_encode(hash('sha256', serialize($value), \true)), 0, 7);
        return str_replace(['/', '+'], ['.', '_'], $hash);
    }
    /**
     * @param string $name
     * @return mixed
     */
    protected function getEnv($name)
    {
        $value = parent::getEnv($name);
        $bag = $this->getParameterBag();
        if (!\is_string($value) || !$bag instanceof EnvPlaceholderParameterBag) {
            return $value;
        }
        $envPlaceholders = $bag->getEnvPlaceholders();
        if (isset($envPlaceholders[$name][$value])) {
            $bag = new ParameterBag($bag->all());
            return $bag->unescapeValue($bag->get("env({$name})"));
        }
        foreach ($envPlaceholders as $env => $placeholders) {
            if (isset($placeholders[$value])) {
                return $this->getEnv($env);
            }
        }
        $this->resolving["env({$name})"] = \true;
        try {
            return $bag->unescapeValue($this->resolveEnvPlaceholders($bag->escapeValue($value), \true));
        } finally {
            unset($this->resolving["env({$name})"]);
        }
    }
    /**
     * @return mixed
     * @param object $service
     */
    private function callMethod($service, array $call, array &$inlineServices)
    {
        foreach (self::getServiceConditionals($call[1]) as $s) {
            if (!$this->has($s)) {
                return $service;
            }
        }
        foreach (self::getInitializedConditionals($call[1]) as $s) {
            if (!$this->doGet($s, ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE, $inlineServices)) {
                return $service;
            }
        }
        $result = $service->{$call[0]}(...$this->doResolveServices($this->getParameterBag()->unescapeValue($this->getParameterBag()->resolveValue($call[1])), $inlineServices));
        return empty($call[2]) ? $service : $result;
    }
    /**
     * @param mixed $service
     */
    private function shareService(Definition $definition, $service, ?string $id, array &$inlineServices)
    {
        $inlineServices[$id ?? spl_object_hash($definition)] = $service;
        if (null !== $id && $definition->isShared()) {
            if ($definition->isPrivate() && $this->isCompiled()) {
                $this->privates[$id] = $service;
            } else {
                $this->services[$id] = $service;
            }
            unset($this->loading[$id]);
        }
    }
    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (!isset($this->expressionLanguage)) {
            if (!class_exists(Expression::class)) {
                throw new LogicException('Expressions cannot be used without the ExpressionLanguage component. Try running "composer require symfony/expression-language".');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders, null, Closure::fromCallable([$this, 'getEnv']));
        }
        return $this->expressionLanguage;
    }
    private function inVendors(string $path): bool
    {
        $this->vendors = $this->vendors ?? (new ComposerResource())->getVendors();
        $path = realpath($path) ?: $path;
        foreach ($this->vendors as $vendor) {
            if (strncmp($path, $vendor, strlen($vendor)) === 0 && \false !== strpbrk(substr($path, \strlen($vendor), 1), '/' . \DIRECTORY_SEPARATOR)) {
                $this->addResource(new FileResource($vendor . '/composer/installed.json'));
                return \true;
            }
        }
        return \false;
    }
}
