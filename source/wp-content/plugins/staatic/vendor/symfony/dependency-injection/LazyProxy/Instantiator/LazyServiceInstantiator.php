<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\Instantiator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\LazyServiceDumper;
final class LazyServiceInstantiator implements InstantiatorInterface
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
        $dumper = new LazyServiceDumper();
        if (!$dumper->isProxyCandidate($definition, $asGhostObject, $id)) {
            throw new InvalidArgumentException(sprintf('Cannot instantiate lazy proxy for service "%s".', $id));
        }
        if (!class_exists($proxyClass = $dumper->getProxyClass($definition, $asGhostObject, $class), \false)) {
            eval($dumper->getProxyCode($definition, $id));
        }
        return $asGhostObject ? $proxyClass::createLazyGhost($realInstantiator) : $proxyClass::createLazyProxy($realInstantiator);
    }
}
