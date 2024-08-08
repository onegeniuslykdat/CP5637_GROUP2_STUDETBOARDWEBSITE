<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\DependencyInjection\TypedReference;
trait PriorityTaggedServiceTrait
{
    /**
     * @param string|TaggedIteratorArgument $tagName
     */
    private function findAndSortTaggedServices($tagName, ContainerBuilder $container): array
    {
        $exclude = [];
        $indexAttribute = $defaultIndexMethod = $needsIndexes = $defaultPriorityMethod = null;
        if ($tagName instanceof TaggedIteratorArgument) {
            $indexAttribute = $tagName->getIndexAttribute();
            $defaultIndexMethod = $tagName->getDefaultIndexMethod();
            $needsIndexes = $tagName->needsIndexes();
            $defaultPriorityMethod = $tagName->getDefaultPriorityMethod() ?? 'getDefaultPriority';
            $exclude = $tagName->getExclude();
            $tagName = $tagName->getTag();
        }
        $i = 0;
        $services = [];
        foreach ($container->findTaggedServiceIds($tagName, \true) as $serviceId => $attributes) {
            if (\in_array($serviceId, $exclude, \true)) {
                continue;
            }
            $defaultPriority = null;
            $defaultIndex = null;
            $definition = $container->getDefinition($serviceId);
            $class = $definition->getClass();
            $class = $container->getParameterBag()->resolveValue($class) ?: null;
            $checkTaggedItem = !$definition->hasTag($definition->isAutoconfigured() ? 'container.ignore_attributes' : $tagName);
            foreach ($attributes as $attribute) {
                $index = $priority = null;
                if (isset($attribute['priority'])) {
                    $priority = $attribute['priority'];
                } elseif (null === $defaultPriority && $defaultPriorityMethod && $class) {
                    $defaultPriority = PriorityTaggedServiceUtil::getDefault($container, $serviceId, $class, $defaultPriorityMethod, $tagName, 'priority', $checkTaggedItem);
                }
                $priority = $priority ?? ($defaultPriority = $defaultPriority ?? 0);
                if (null === $indexAttribute && !$defaultIndexMethod && !$needsIndexes) {
                    $services[] = [$priority, ++$i, null, $serviceId, null];
                    continue 2;
                }
                if (null !== $indexAttribute && isset($attribute[$indexAttribute])) {
                    $index = $attribute[$indexAttribute];
                } elseif (null === $defaultIndex && $defaultPriorityMethod && $class) {
                    $defaultIndex = PriorityTaggedServiceUtil::getDefault($container, $serviceId, $class, $defaultIndexMethod ?? 'getDefaultName', $tagName, $indexAttribute, $checkTaggedItem);
                }
                $index = $index ?? ($defaultIndex = $defaultIndex ?? $serviceId);
                $services[] = [$priority, ++$i, $index, $serviceId, $class];
            }
        }
        uasort($services, static function ($a, $b) {
            return ($b[0] <=> $a[0]) ?: ($a[1] <=> $b[1]);
        });
        $refs = [];
        foreach ($services as [, , $index, $serviceId, $class]) {
            if (!$class) {
                $reference = new Reference($serviceId);
            } elseif ($index === $serviceId) {
                $reference = new TypedReference($serviceId, $class);
            } else {
                $reference = new TypedReference($serviceId, $class, ContainerBuilder::EXCEPTION_ON_INVALID_REFERENCE, $index);
            }
            if (null === $index) {
                $refs[] = $reference;
            } else {
                $refs[$index] = $reference;
            }
        }
        return $refs;
    }
}
class PriorityTaggedServiceUtil
{
    /**
     * @param ContainerBuilder $container
     * @param string $serviceId
     * @param string $class
     * @param string $defaultMethod
     * @param string $tagName
     * @param string|null $indexAttribute
     * @param bool $checkTaggedItem
     * @return int|string|null
     */
    public static function getDefault($container, $serviceId, $class, $defaultMethod, $tagName, $indexAttribute, $checkTaggedItem)
    {
        if (!($r = $container->getReflectionClass($class)) || !$checkTaggedItem && !$r->hasMethod($defaultMethod)) {
            return null;
        }
        if ($checkTaggedItem && !$r->hasMethod($defaultMethod)) {
            foreach (method_exists($r, 'getAttributes') ? $r->getAttributes(AsTaggedItem::class) : [] as $attribute) {
                return ('priority' === $indexAttribute) ? $attribute->newInstance()->priority : $attribute->newInstance()->index;
            }
            return null;
        }
        if (null !== $indexAttribute) {
            $service = ($class !== $serviceId) ? sprintf('service "%s"', $serviceId) : 'on the corresponding service';
            $message = [sprintf('Either method "%s::%s()" should ', $class, $defaultMethod), sprintf(' or tag "%s" on %s is missing attribute "%s".', $tagName, $service, $indexAttribute)];
        } else {
            $message = [sprintf('Method "%s::%s()" should ', $class, $defaultMethod), '.'];
        }
        if (!($rm = $r->getMethod($defaultMethod))->isStatic()) {
            throw new InvalidArgumentException(implode('be static', $message));
        }
        if (!$rm->isPublic()) {
            throw new InvalidArgumentException(implode('be public', $message));
        }
        $default = $rm->invoke(null);
        if ('priority' === $indexAttribute) {
            if (!\is_int($default)) {
                throw new InvalidArgumentException(implode(sprintf('return int (got "%s")', get_debug_type($default)), $message));
            }
            return $default;
        }
        if (\is_int($default)) {
            $default = (string) $default;
        }
        if (!\is_string($default)) {
            throw new InvalidArgumentException(implode(sprintf('return string|int (got "%s")', get_debug_type($default)), $message));
        }
        return $default;
    }
}
