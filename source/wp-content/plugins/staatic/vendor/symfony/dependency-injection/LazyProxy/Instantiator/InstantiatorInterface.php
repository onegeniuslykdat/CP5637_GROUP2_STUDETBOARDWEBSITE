<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
interface InstantiatorInterface
{
    /**
     * @param ContainerInterface $container
     * @param Definition $definition
     * @param string $id
     * @param callable $realInstantiator
     */
    public function instantiateProxy($container, $definition, $id, $realInstantiator);
}
