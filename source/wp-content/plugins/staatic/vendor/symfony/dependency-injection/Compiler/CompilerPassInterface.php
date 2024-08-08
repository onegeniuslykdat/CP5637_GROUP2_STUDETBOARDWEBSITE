<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
interface CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container);
}
