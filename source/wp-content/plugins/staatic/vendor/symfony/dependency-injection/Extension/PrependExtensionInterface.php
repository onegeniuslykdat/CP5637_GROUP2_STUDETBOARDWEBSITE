<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Extension;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
interface PrependExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function prepend($container);
}
