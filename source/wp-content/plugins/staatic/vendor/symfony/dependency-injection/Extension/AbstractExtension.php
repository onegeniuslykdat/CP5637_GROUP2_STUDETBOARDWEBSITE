<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\Config\Definition\Configuration;
use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
abstract class AbstractExtension extends Extension implements ConfigurableExtensionInterface, PrependExtensionInterface
{
    use ExtensionTrait;
    /**
     * @param DefinitionConfigurator $definition
     */
    public function configure($definition): void
    {
    }
    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     */
    public function prependExtension($container, $builder): void
    {
    }
    /**
     * @param mixed[] $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     */
    public function loadExtension($config, $container, $builder): void
    {
    }
    /**
     * @param mixed[] $config
     * @param ContainerBuilder $container
     */
    public function getConfiguration($config, $container): ?ConfigurationInterface
    {
        return new Configuration($this, $container, $this->getAlias());
    }
    /**
     * @param ContainerBuilder $container
     */
    final public function prepend($container): void
    {
        $callback = function (ContainerConfigurator $configurator) use ($container) {
            $this->prependExtension($configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this);
    }
    /**
     * @param mixed[] $configs
     * @param ContainerBuilder $container
     */
    final public function load($configs, $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $callback = function (ContainerConfigurator $configurator) use ($config, $container) {
            $this->loadExtension($config, $configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this);
    }
}
