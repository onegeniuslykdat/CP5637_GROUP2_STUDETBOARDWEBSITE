<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class RegisterReverseContainerPass implements CompilerPassInterface
{
    /**
     * @var bool
     */
    private $beforeRemoving;
    public function __construct(bool $beforeRemoving)
    {
        $this->beforeRemoving = $beforeRemoving;
    }
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        if (!$container->hasDefinition('reverse_container')) {
            return;
        }
        $refType = $this->beforeRemoving ? ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE : ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
        $services = [];
        foreach ($container->findTaggedServiceIds('container.reversible') as $id => $tags) {
            $services[$id] = new Reference($id, $refType);
        }
        if ($this->beforeRemoving) {
            $services['reverse_container'] = new Reference('reverse_container', $refType);
        }
        $locator = $container->getDefinition('reverse_container')->getArgument(1);
        if ($locator instanceof Reference) {
            $locator = $container->getDefinition((string) $locator);
        }
        if ($locator instanceof Definition) {
            foreach ($services as $id => $ref) {
                $services[$id] = new ServiceClosureArgument($ref);
            }
            $locator->replaceArgument(0, $services);
        } else {
            $locator->setValues($services);
        }
    }
}
