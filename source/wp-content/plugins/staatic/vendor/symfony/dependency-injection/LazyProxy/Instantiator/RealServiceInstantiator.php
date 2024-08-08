<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class RealServiceInstantiator implements InstantiatorInterface
{
    /**
     * @param ContainerInterface $container
     * @param Definition $definition
     * @param string $id
     * @param callable $realInstantiator
     * @return object
     */
    public function instantiateProxy($container, $definition, $id, $realInstantiator)
    {
        return $realInstantiator();
    }
}
