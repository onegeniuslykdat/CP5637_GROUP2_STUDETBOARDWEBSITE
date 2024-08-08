<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

use Closure;
use ReflectionFunction;
use ReflectionAttribute;
use ReflectionNamedType;
use LogicException;
use Staatic\Vendor\Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Staatic\Vendor\Symfony\Component\Config\Builder\ConfigBuilderGeneratorInterface;
use Staatic\Vendor\Symfony\Component\Config\Builder\ConfigBuilderInterface;
use Staatic\Vendor\Symfony\Component\Config\FileLocatorInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\When;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Container;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
class PhpFileLoader extends FileLoader
{
    protected $autoRegisterAliasesForSinglyImplementedInterfaces = \false;
    /**
     * @var ConfigBuilderGeneratorInterface|null
     */
    private $generator;
    public function __construct(ContainerBuilder $container, FileLocatorInterface $locator, string $env = null, ConfigBuilderGeneratorInterface $generator = null)
    {
        parent::__construct($container, $locator, $env);
        $this->generator = $generator;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        $container = $this->container;
        $loader = $this;
        $path = $this->locator->locate($resource);
        $this->setCurrentDir(\dirname($path));
        $this->container->fileExists($path);
        $load = Closure::bind(function ($path, $env) use ($container, $loader, $resource, $type) {
            return include $path;
        }, $this, ProtectedPhpFileLoader::class);
        try {
            $callback = $load($path, $this->env);
            if (\is_object($callback) && \is_callable($callback)) {
                $this->executeCallback($callback, new ContainerConfigurator($this->container, $this, $this->instanceof, $path, $resource, $this->env), $path);
            }
        } finally {
            $this->instanceof = [];
            $this->registerAliasesForSinglyImplementedInterfaces();
        }
        return null;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        if (!\is_string($resource)) {
            return \false;
        }
        if (null === $type && 'php' === pathinfo($resource, \PATHINFO_EXTENSION)) {
            return \true;
        }
        return 'php' === $type;
    }
    private function executeCallback(callable $callback, ContainerConfigurator $containerConfigurator, string $path)
    {
        $callback = Closure::fromCallable($callback);
        $arguments = [];
        $configBuilders = [];
        $r = new ReflectionFunction($callback);
        $attribute = null;
        foreach (method_exists($r, 'getAttributes') ? $r->getAttributes(When::class, ReflectionAttribute::IS_INSTANCEOF) : [] as $attribute) {
            if ($this->env === $attribute->newInstance()->env) {
                $attribute = null;
                break;
            }
        }
        if (null !== $attribute) {
            return;
        }
        foreach ($r->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();
            if (!$reflectionType instanceof ReflectionNamedType) {
                throw new \InvalidArgumentException(sprintf('Could not resolve argument "$%s" for "%s". You must typehint it (for example with "%s" or "%s").', $parameter->getName(), $path, ContainerConfigurator::class, ContainerBuilder::class));
            }
            $type = $reflectionType->getName();
            switch ($type) {
                case ContainerConfigurator::class:
                    $arguments[] = $containerConfigurator;
                    break;
                case ContainerBuilder::class:
                    $arguments[] = $this->container;
                    break;
                case FileLoader::class:
                case self::class:
                    $arguments[] = $this;
                    break;
                case 'string':
                    if (null !== $this->env && 'env' === $parameter->getName()) {
                        $arguments[] = $this->env;
                        break;
                    }
                default:
                    try {
                        $configBuilder = $this->configBuilder($type);
                    } catch (InvalidArgumentException|LogicException $e) {
                        throw new \InvalidArgumentException(sprintf('Could not resolve argument "%s" for "%s".', $type . ' $' . $parameter->getName(), $path), 0, $e);
                    }
                    $configBuilders[] = $configBuilder;
                    $arguments[] = $configBuilder;
            }
        }
        class_exists(ContainerConfigurator::class);
        $callback(...$arguments);
        foreach ($configBuilders as $configBuilder) {
            $containerConfigurator->extension($configBuilder->getExtensionAlias(), $configBuilder->toArray());
        }
    }
    private function configBuilder(string $namespace): ConfigBuilderInterface
    {
        if (!class_exists(ConfigBuilderGenerator::class)) {
            throw new LogicException('You cannot use the config builder as the Config component is not installed. Try running "composer require symfony/config".');
        }
        if (null === $this->generator) {
            throw new LogicException('You cannot use the ConfigBuilders without providing a class implementing ConfigBuilderGeneratorInterface.');
        }
        if (class_exists($namespace) && is_subclass_of($namespace, ConfigBuilderInterface::class)) {
            return new $namespace();
        }
        if (strncmp($namespace, 'Symfony\Config\\', strlen('Symfony\Config\\')) !== 0) {
            throw new InvalidArgumentException(sprintf('Could not find or generate class "%s".', $namespace));
        }
        $alias = Container::underscore(substr($namespace, 15, -6));
        if (strpos($alias, '\\') !== false) {
            throw new InvalidArgumentException('You can only use "root" ConfigBuilders from "Symfony\Config\" namespace. Nested classes like "Symfony\Config\Framework\CacheConfig" cannot be used.');
        }
        if (!$this->container->hasExtension($alias)) {
            $extensions = array_filter(array_map(function (ExtensionInterface $ext) {
                return $ext->getAlias();
            }, $this->container->getExtensions()));
            throw new InvalidArgumentException(sprintf('There is no extension able to load the configuration for "%s". Looked for namespace "%s", found "%s".', $namespace, $alias, $extensions ? implode('", "', $extensions) : 'none'));
        }
        $extension = $this->container->getExtension($alias);
        if (!$extension instanceof ConfigurationExtensionInterface) {
            throw new LogicException(sprintf('You cannot use the config builder for "%s" because the extension does not implement "%s".', $namespace, ConfigurationExtensionInterface::class));
        }
        $configuration = $extension->getConfiguration([], $this->container);
        $loader = $this->generator->build($configuration);
        return $loader();
    }
}
final class ProtectedPhpFileLoader extends PhpFileLoader
{
}
