<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use ReflectionClass;
use ReflectionAttribute;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
final class AutowireAsDecoratorPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($this->accept($definition) && $reflectionClass = $container->getReflectionClass($definition->getClass(), \false)) {
                $this->processClass($definition, $reflectionClass);
            }
        }
    }
    private function accept(Definition $definition): bool
    {
        return !$definition->hasTag('container.ignore_attributes') && $definition->isAutowired();
    }
    private function processClass(Definition $definition, ReflectionClass $reflectionClass)
    {
        foreach (method_exists($reflectionClass, 'getAttributes') ? $reflectionClass->getAttributes(AsDecorator::class, ReflectionAttribute::IS_INSTANCEOF) : [] as $attribute) {
            $attribute = $attribute->newInstance();
            $definition->setDecoratedService($attribute->decorates, null, $attribute->priority, $attribute->onInvalid);
        }
    }
}
