<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
class ResolveInstanceofConditionalsPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getAutoconfiguredInstanceof() as $interface => $definition) {
            if ($definition->getArguments()) {
                throw new InvalidArgumentException(sprintf('Autoconfigured instanceof for type "%s" defines arguments but these are not supported and should be removed.', $interface));
            }
        }
        $tagsToKeep = [];
        if ($container->hasParameter('container.behavior_describing_tags')) {
            $tagsToKeep = $container->getParameter('container.behavior_describing_tags');
        }
        foreach ($container->getDefinitions() as $id => $definition) {
            $container->setDefinition($id, $this->processDefinition($container, $id, $definition, $tagsToKeep));
        }
        if ($container->hasParameter('container.behavior_describing_tags')) {
            $container->getParameterBag()->remove('container.behavior_describing_tags');
        }
    }
    private function processDefinition(ContainerBuilder $container, string $id, Definition $definition, array $tagsToKeep): Definition
    {
        $instanceofConditionals = $definition->getInstanceofConditionals();
        $autoconfiguredInstanceof = $definition->isAutoconfigured() ? $container->getAutoconfiguredInstanceof() : [];
        if (!$instanceofConditionals && !$autoconfiguredInstanceof) {
            return $definition;
        }
        if (!$class = $container->getParameterBag()->resolveValue($definition->getClass())) {
            return $definition;
        }
        $conditionals = $this->mergeConditionals($autoconfiguredInstanceof, $instanceofConditionals, $container);
        $definition->setInstanceofConditionals([]);
        $shared = null;
        $instanceofTags = [];
        $instanceofCalls = [];
        $instanceofBindings = [];
        $reflectionClass = null;
        $parent = ($definition instanceof ChildDefinition) ? $definition->getParent() : null;
        foreach ($conditionals as $interface => $instanceofDefs) {
            if ($interface !== $class && !($reflectionClass = $reflectionClass ?? ($container->getReflectionClass($class, \false) ?: \false))) {
                continue;
            }
            if ($interface !== $class && !is_subclass_of($class, $interface)) {
                continue;
            }
            foreach ($instanceofDefs as $key => $instanceofDef) {
                $instanceofDef = clone $instanceofDef;
                $instanceofDef->setAbstract(\true)->setParent($parent ?: ('.abstract.instanceof.' . $id));
                $parent = '.instanceof.' . $interface . '.' . $key . '.' . $id;
                $container->setDefinition($parent, $instanceofDef);
                $instanceofTags[] = [$interface, $instanceofDef->getTags()];
                $instanceofBindings = $instanceofDef->getBindings() + $instanceofBindings;
                foreach ($instanceofDef->getMethodCalls() as $methodCall) {
                    $instanceofCalls[] = $methodCall;
                }
                $instanceofDef->setTags([]);
                $instanceofDef->setMethodCalls([]);
                $instanceofDef->setBindings([]);
                if (isset($instanceofDef->getChanges()['shared'])) {
                    $shared = $instanceofDef->isShared();
                }
            }
        }
        if ($parent) {
            $bindings = $definition->getBindings();
            $abstract = $container->setDefinition('.abstract.instanceof.' . $id, $definition);
            $definition->setBindings([]);
            $definition = serialize($definition);
            if (Definition::class === get_class($abstract)) {
                $definition = substr_replace($definition, '53', 2, 2);
                $definition = substr_replace($definition, 'Child', 44, 0);
            }
            $definition = unserialize($definition);
            $definition->setParent($parent);
            if (null !== $shared && !isset($definition->getChanges()['shared'])) {
                $definition->setShared($shared);
            }
            $i = \count($instanceofTags);
            while (0 <= --$i) {
                [$interface, $tags] = $instanceofTags[$i];
                foreach ($tags as $k => $v) {
                    if (null === $definition->getDecoratedService() || $interface === $definition->getClass() || \in_array($k, $tagsToKeep, \true)) {
                        foreach ($v as $v) {
                            if ($definition->hasTag($k) && \in_array($v, $definition->getTag($k))) {
                                continue;
                            }
                            $definition->addTag($k, $v);
                        }
                    }
                }
            }
            $definition->setMethodCalls(array_merge($instanceofCalls, $definition->getMethodCalls()));
            $definition->setBindings($bindings + $instanceofBindings);
            $abstract->setBindings([])->setArguments([])->setMethodCalls([])->setDecoratedService(null)->setTags([])->setAbstract(\true);
        }
        return $definition;
    }
    private function mergeConditionals(array $autoconfiguredInstanceof, array $instanceofConditionals, ContainerBuilder $container): array
    {
        $conditionals = array_map(function ($childDef) {
            return [$childDef];
        }, $autoconfiguredInstanceof);
        foreach ($instanceofConditionals as $interface => $instanceofDef) {
            if (!$container->getReflectionClass($interface)) {
                throw new RuntimeException(sprintf('"%s" is set as an "instanceof" conditional, but it does not exist.', $interface));
            }
            if (!isset($autoconfiguredInstanceof[$interface])) {
                $conditionals[$interface] = [];
            }
            $conditionals[$interface][] = $instanceofDef;
        }
        return $conditionals;
    }
}
