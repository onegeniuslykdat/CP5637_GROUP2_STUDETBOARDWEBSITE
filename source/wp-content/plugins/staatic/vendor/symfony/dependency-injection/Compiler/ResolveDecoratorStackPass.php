<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ResolveDecoratorStackPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $stacks = [];
        foreach ($container->findTaggedServiceIds('container.stack') as $id => $tags) {
            $definition = $container->getDefinition($id);
            if (!$definition instanceof ChildDefinition) {
                throw new InvalidArgumentException(sprintf('Invalid service "%s": only definitions with a "parent" can have the "container.stack" tag.', $id));
            }
            if (!$stack = $definition->getArguments()) {
                throw new InvalidArgumentException(sprintf('Invalid service "%s": the stack of decorators is empty.', $id));
            }
            $stacks[$id] = $stack;
        }
        if (!$stacks) {
            return;
        }
        $resolvedDefinitions = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!isset($stacks[$id])) {
                $resolvedDefinitions[$id] = $definition;
                continue;
            }
            foreach (array_reverse($this->resolveStack($stacks, [$id]), \true) as $k => $v) {
                $resolvedDefinitions[$k] = $v;
            }
            $alias = $container->setAlias($id, $k);
            if ($definition->getChanges()['public'] ?? \false) {
                $alias->setPublic($definition->isPublic());
            }
            if ($definition->isDeprecated()) {
                $alias->setDeprecated(...array_values($definition->getDeprecation('%alias_id%')));
            }
        }
        $container->setDefinitions($resolvedDefinitions);
    }
    private function resolveStack(array $stacks, array $path): array
    {
        $definitions = [];
        $id = end($path);
        $prefix = '.' . $id . '.';
        if (!isset($stacks[$id])) {
            return [$id => new ChildDefinition($id)];
        }
        if (key($path) !== $searchKey = array_search($id, $path)) {
            throw new ServiceCircularReferenceException($id, \array_slice($path, $searchKey));
        }
        foreach ($stacks[$id] as $k => $definition) {
            if ($definition instanceof ChildDefinition && isset($stacks[$definition->getParent()])) {
                $path[] = $definition->getParent();
                $definition = unserialize(serialize($definition));
            } elseif ($definition instanceof Definition) {
                $definitions[$decoratedId = $prefix . $k] = $definition;
                continue;
            } elseif ($definition instanceof Reference || $definition instanceof Alias) {
                $path[] = (string) $definition;
            } else {
                throw new InvalidArgumentException(sprintf('Invalid service "%s": unexpected value of type "%s" found in the stack of decorators.', $id, get_debug_type($definition)));
            }
            $p = $prefix . $k;
            foreach ($this->resolveStack($stacks, $path) as $k => $v) {
                $definitions[$decoratedId = $p . $k] = ($definition instanceof ChildDefinition) ? $definition->setParent($k) : new ChildDefinition($k);
                $definition = null;
            }
            array_pop($path);
        }
        if (1 === \count($path)) {
            foreach ($definitions as $k => $definition) {
                $definition->setPublic(\false)->setTags([])->setDecoratedService($decoratedId);
            }
            $definition->setDecoratedService(null);
        }
        return $definitions;
    }
}
