<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurableInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
interface ConfigurableExtensionInterface extends ConfigurableInterface
{
    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     */
    public function prependExtension($container, $builder): void;
    /**
     * @param mixed[] $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     */
    public function loadExtension($config, $container, $builder): void;
}
