<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\Processor;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Container;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
abstract class Extension implements ExtensionInterface, ConfigurationExtensionInterface
{
    /**
     * @var mixed[]
     */
    private $processedConfigs = [];
    public function getXsdValidationBasePath()
    {
        return \false;
    }
    public function getNamespace()
    {
        return 'http://example.org/schema/dic/' . $this->getAlias();
    }
    public function getAlias(): string
    {
        $className = static::class;
        if (substr_compare($className, 'Extension', -strlen('Extension')) !== 0) {
            throw new BadMethodCallException('This extension does not follow the naming convention; you must overwrite the getAlias() method.');
        }
        $classBaseName = substr(strrchr($className, '\\'), 1, -9);
        return Container::underscore($classBaseName);
    }
    /**
     * @param mixed[] $config
     * @param ContainerBuilder $container
     */
    public function getConfiguration($config, $container)
    {
        $class = static::class;
        if (strpos($class, "\x00") !== false) {
            return null;
        }
        $class = substr_replace($class, '\Configuration', strrpos($class, '\\'));
        $class = $container->getReflectionClass($class);
        if (!$class) {
            return null;
        }
        if (!$class->implementsInterface(ConfigurationInterface::class)) {
            throw new LogicException(sprintf('The extension configuration class "%s" must implement "%s".', $class->getName(), ConfigurationInterface::class));
        }
        if (!($constructor = $class->getConstructor()) || !$constructor->getNumberOfRequiredParameters()) {
            return $class->newInstance();
        }
        return null;
    }
    /**
     * @param ConfigurationInterface $configuration
     * @param mixed[] $configs
     */
    final protected function processConfiguration($configuration, $configs): array
    {
        $processor = new Processor();
        return $this->processedConfigs[] = $processor->processConfiguration($configuration, $configs);
    }
    final public function getProcessedConfigs(): array
    {
        try {
            return $this->processedConfigs;
        } finally {
            $this->processedConfigs = [];
        }
    }
    /**
     * @param ContainerBuilder $container
     * @param mixed[] $config
     */
    protected function isConfigEnabled($container, $config): bool
    {
        if (!\array_key_exists('enabled', $config)) {
            throw new InvalidArgumentException("The config array has no 'enabled' key.");
        }
        return (bool) $container->getParameterBag()->resolveValue($config['enabled']);
    }
}
