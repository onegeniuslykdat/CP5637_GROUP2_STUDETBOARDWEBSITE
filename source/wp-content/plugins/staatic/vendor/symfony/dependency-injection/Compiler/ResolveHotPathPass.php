<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ResolveHotPathPass extends AbstractRecursivePass
{
    /**
     * @var mixed[]
     */
    private $resolvedIds = [];
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        try {
            parent::process($container);
            $container->getDefinition('service_container')->clearTag('container.hot_path');
        } finally {
            $this->resolvedIds = [];
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof ArgumentInterface) {
            return $value;
        }
        if ($value instanceof Definition && $isRoot) {
            if ($value->isDeprecated()) {
                return $value->clearTag('container.hot_path');
            }
            $this->resolvedIds[$this->currentId] = \true;
            if (!$value->hasTag('container.hot_path')) {
                return $value;
            }
        }
        if ($value instanceof Reference && ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE !== $value->getInvalidBehavior() && $this->container->hasDefinition($id = (string) $value)) {
            $definition = $this->container->getDefinition($id);
            if ($definition->isDeprecated() || $definition->hasTag('container.hot_path')) {
                return $value;
            }
            $definition->addTag('container.hot_path');
            if (isset($this->resolvedIds[$id])) {
                parent::processValue($definition, \false);
            }
            return $value;
        }
        return parent::processValue($value, $isRoot);
    }
}
