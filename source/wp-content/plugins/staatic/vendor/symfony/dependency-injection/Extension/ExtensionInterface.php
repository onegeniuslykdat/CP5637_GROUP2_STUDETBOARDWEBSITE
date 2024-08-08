<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
interface ExtensionInterface
{
    /**
     * @param mixed[] $configs
     * @param ContainerBuilder $container
     */
    public function load($configs, $container);
    public function getNamespace();
    public function getXsdValidationBasePath();
    public function getAlias();
}
