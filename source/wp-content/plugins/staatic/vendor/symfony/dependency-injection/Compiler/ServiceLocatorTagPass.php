<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
final class ServiceLocatorTagPass extends AbstractRecursivePass
{
    use PriorityTaggedServiceTrait;
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof ServiceLocatorArgument) {
            if ($value->getTaggedIteratorArgument()) {
                $value->setValues($this->findAndSortTaggedServices($value->getTaggedIteratorArgument(), $this->container));
            }
            return self::register($this->container, $value->getValues());
        }
        if ($value instanceof Definition) {
            $value->setBindings(parent::processValue($value->getBindings()));
        }
        if (!$value instanceof Definition || !$value->hasTag('container.service_locator')) {
            return parent::processValue($value, $isRoot);
        }
        if (!$value->getClass()) {
            $value->setClass(ServiceLocator::class);
        }
        $services = $value->getArguments()[0] ?? null;
        if ($services instanceof TaggedIteratorArgument) {
            $services = $this->findAndSortTaggedServices($services, $this->container);
        }
        if (!\is_array($services)) {
            throw new InvalidArgumentException(sprintf('Invalid definition for service "%s": an array of references is expected as first argument when the "container.service_locator" tag is set.', $this->currentId));
        }
        $i = 0;
        foreach ($services as $k => $v) {
            if ($v instanceof ServiceClosureArgument) {
                continue;
            }
            if ($i === $k) {
                if ($v instanceof Reference) {
                    unset($services[$k]);
                    $k = (string) $v;
                }
                ++$i;
            } elseif (\is_int($k)) {
                $i = null;
            }
            $services[$k] = new ServiceClosureArgument($v);
        }
        ksort($services);
        $value->setArgument(0, $services);
        $id = '.service_locator.' . ContainerBuilder::hash($value);
        if ($isRoot) {
            if ($id !== $this->currentId) {
                $this->container->setAlias($id, new Alias($this->currentId, \false));
            }
            return $value;
        }
        $this->container->setDefinition($id, $value->setPublic(\false));
        return new Reference($id);
    }
    /**
     * @param ContainerBuilder $container
     * @param mixed[] $map
     * @param string|null $callerId
     */
    public static function register($container, $map, $callerId = null): Reference
    {
        foreach ($map as $k => $v) {
            $map[$k] = new ServiceClosureArgument($v);
        }
        $locator = (new Definition(ServiceLocator::class))->addArgument($map)->addTag('container.service_locator');
        if (null !== $callerId && $container->hasDefinition($callerId)) {
            $locator->setBindings($container->getDefinition($callerId)->getBindings());
        }
        if (!$container->hasDefinition($id = '.service_locator.' . ContainerBuilder::hash($locator))) {
            $container->setDefinition($id, $locator);
        }
        if (null !== $callerId) {
            $locatorId = $id;
            $container->register($id .= '.' . $callerId, ServiceLocator::class)->setFactory([new Reference($locatorId), 'withContext'])->addTag('container.service_locator_context', ['id' => $callerId])->addArgument($callerId)->addArgument(new Reference('service_container'));
        }
        return new Reference($id);
    }
}
