<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
interface ConfigurationExtensionInterface
{
    /**
     * @param mixed[] $config
     * @param ContainerBuilder $container
     */
    public function getConfiguration($config, $container);
}
