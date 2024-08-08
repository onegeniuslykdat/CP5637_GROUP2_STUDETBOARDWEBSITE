<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

use ReflectionClass;
use Closure;
use ReflectionMethod;
use ReflectionProperty;
class LazyObjectRegistry
{
    /**
     * @var mixed[]
     */
    public static $classReflectors = [];
    /**
     * @var mixed[]
     */
    public static $defaultProperties = [];
    /**
     * @var mixed[]
     */
    public static $classResetters = [];
    /**
     * @var mixed[]
     */
    public static $classAccessors = [];
    /**
     * @var mixed[]
     */
    public static $parentMethods = [];
    /**
     * @var Closure|null
     */
    public static $noInitializerState;
    public static function getClassResetters($class)
    {
        $classProperties = [];
        if ((self::$classReflectors[$class] = self::$classReflectors[$class] ?? new ReflectionClass($class))->isInternal()) {
            $propertyScopes = [];
        } else {
            $propertyScopes = Hydrator::$propertyScopes[$class] = Hydrator::$propertyScopes[$class] ?? Hydrator::getPropertyScopes($class);
        }
        foreach ($propertyScopes as $key => [$scope, $name, $readonlyScope]) {
            $propertyScopes[$k = "\x00{$scope}\x00{$name}"] ?? $propertyScopes[$k = "\x00*\x00{$name}"] ?? $k = $name;
            if ($k === $key && "\x00{$class}\x00lazyObjectState" !== $k) {
                $classProperties[$readonlyScope ?? $scope][$name] = $key;
            }
        }
        $resetters = [];
        foreach ($classProperties as $scope => $properties) {
            $resetters[] = Closure::bind(static function ($instance, $skippedProperties, $onlyProperties = null) use ($properties) {
                foreach ($properties as $name => $key) {
                    if (!\array_key_exists($key, $skippedProperties) && (null === $onlyProperties || \array_key_exists($key, $onlyProperties))) {
                        unset($instance->{$name});
                    }
                }
            }, null, $scope);
        }
        $resetters[] = static function ($instance, $skippedProperties, $onlyProperties = null) {
            foreach ((array) $instance as $name => $value) {
                if ("\x00" !== ($name[0] ?? '') && !\array_key_exists($name, $skippedProperties) && (null === $onlyProperties || \array_key_exists($name, $onlyProperties))) {
                    unset($instance->{$name});
                }
            }
        };
        return $resetters;
    }
    public static function getClassAccessors($class)
    {
        return Closure::bind(static function () {
            return ['get' => static function &($instance, $name, $readonly) {
                if (!$readonly) {
                    return $instance->{$name};
                }
                $value = $instance->{$name};
                return $value;
            }, 'set' => static function ($instance, $name, $value) {
                $instance->{$name} = $value;
            }, 'isset' => static function ($instance, $name) {
                return isset($instance->{$name});
            }, 'unset' => static function ($instance, $name) {
                unset($instance->{$name});
            }];
        }, null, (Closure::class === $class) ? null : $class)();
    }
    public static function getParentMethods($class)
    {
        $parent = get_parent_class($class);
        $methods = [];
        foreach (['set', 'isset', 'unset', 'clone', 'serialize', 'unserialize', 'sleep', 'wakeup', 'destruct', 'get'] as $method) {
            if (!$parent || !method_exists($parent, '__' . $method)) {
                $methods[$method] = \false;
            } else {
                $m = new ReflectionMethod($parent, '__' . $method);
                $methods[$method] = !$m->isAbstract() && !$m->isPrivate();
            }
        }
        $methods['get'] = $methods['get'] ? $m->returnsReference() ? 2 : 1 : 0;
        return $methods;
    }
    public static function getScope($propertyScopes, $class, $property, $readonlyScope = null)
    {
        if (null === $readonlyScope && !isset($propertyScopes[$k = "\x00{$class}\x00{$property}"]) && !isset($propertyScopes[$k = "\x00*\x00{$property}"])) {
            return null;
        }
        $frame = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2];
        if (ReflectionProperty::class === $scope = $frame['class'] ?? Closure::class) {
            $scope = $frame['object']->class;
        }
        if (null === $readonlyScope && '*' === $k[1] && ($class === $scope || is_subclass_of($class, $scope) && !isset($propertyScopes["\x00{$scope}\x00{$property}"]))) {
            return null;
        }
        return $scope;
    }
}
