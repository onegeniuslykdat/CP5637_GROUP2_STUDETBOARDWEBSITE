<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class RemoveUnusedDefinitionsPass extends AbstractRecursivePass
{
    /**
     * @var mixed[]
     */
    private $connectedIds = [];
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        try {
            $this->enableExpressionProcessing();
            $this->container = $container;
            $connectedIds = [];
            $aliases = $container->getAliases();
            foreach ($aliases as $id => $alias) {
                if ($alias->isPublic()) {
                    $this->connectedIds[] = (string) $aliases[$id];
                }
            }
            foreach ($container->getDefinitions() as $id => $definition) {
                if ($definition->isPublic()) {
                    $connectedIds[$id] = \true;
                    $this->processValue($definition);
                }
            }
            while ($this->connectedIds) {
                $ids = $this->connectedIds;
                $this->connectedIds = [];
                foreach ($ids as $id) {
                    if (!isset($connectedIds[$id]) && $container->hasDefinition($id)) {
                        $connectedIds[$id] = \true;
                        $this->processValue($container->getDefinition($id));
                    }
                }
            }
            foreach ($container->getDefinitions() as $id => $definition) {
                if (!isset($connectedIds[$id])) {
                    $container->removeDefinition($id);
                    $container->resolveEnvPlaceholders((!$definition->hasErrors()) ? serialize($definition) : $definition);
                    $container->log($this, sprintf('Removed service "%s"; reason: unused.', $id));
                }
            }
        } finally {
            $this->container = null;
            $this->connectedIds = [];
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if (!$value instanceof Reference) {
            return parent::processValue($value, $isRoot);
        }
        if (ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE !== $value->getInvalidBehavior()) {
            $this->connectedIds[] = (string) $value;
        }
        return $value;
    }
}
