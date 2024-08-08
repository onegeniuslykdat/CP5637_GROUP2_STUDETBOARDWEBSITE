<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
final class RegisterAutoconfigureAttributesPass implements CompilerPassInterface
{
    private static $registerForAutoconfiguration;
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($this->accept($definition) && $class = $container->getReflectionClass($definition->getClass(), \false)) {
                $this->processClass($container, $class);
            }
        }
    }
    /**
     * @param Definition $definition
     */
    public function accept($definition): bool
    {
        return $definition->isAutoconfigured() && !$definition->hasTag('container.ignore_attributes');
    }
    /**
     * @param ContainerBuilder $container
     * @param ReflectionClass $class
     */
    public function processClass($container, $class)
    {
        foreach (method_exists($class, 'getAttributes') ? $class->getAttributes(Autoconfigure::class, ReflectionAttribute::IS_INSTANCEOF) : [] as $attribute) {
            self::registerForAutoconfiguration($container, $class, $attribute);
        }
    }
    private static function registerForAutoconfiguration(ContainerBuilder $container, ReflectionClass $class, ReflectionAttribute $attribute)
    {
        if (self::$registerForAutoconfiguration) {
            return (self::$registerForAutoconfiguration)($container, $class, $attribute);
        }
        $parseDefinitions = new ReflectionMethod(YamlFileLoader::class, 'parseDefinitions');
        $yamlLoader = $parseDefinitions->getDeclaringClass()->newInstanceWithoutConstructor();
        self::$registerForAutoconfiguration = static function (ContainerBuilder $container, ReflectionClass $class, ReflectionAttribute $attribute) use ($parseDefinitions, $yamlLoader) {
            $attribute = (array) $attribute->newInstance();
            foreach ($attribute['tags'] ?? [] as $i => $tag) {
                if (\is_array($tag) && [0] === array_keys($tag)) {
                    $attribute['tags'][$i] = [$class->name => $tag[0]];
                }
            }
            $parseDefinitions->invoke($yamlLoader, ['services' => ['_instanceof' => [$class->name => [$container->registerForAutoconfiguration($class->name)] + $attribute]]], $class->getFileName(), \false);
        };
        return (self::$registerForAutoconfiguration)($container, $class, $attribute);
    }
}
