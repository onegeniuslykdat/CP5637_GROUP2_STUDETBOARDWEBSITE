<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
class ExtensionCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getExtensions() as $extension) {
            if (!$extension instanceof CompilerPassInterface) {
                continue;
            }
            $extension->process($container);
        }
    }
}
