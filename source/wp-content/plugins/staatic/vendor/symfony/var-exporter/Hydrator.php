<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter;

use Staatic\Vendor\Symfony\Component\VarExporter\Internal\Hydrator as InternalHydrator;
final class Hydrator
{
    /**
     * @param object $instance
     * @return object
     */
    public static function hydrate($instance, array $properties = [], array $scopedProperties = [])
    {
        if ($properties) {
            $class = get_class($instance);
            $propertyScopes = InternalHydrator::$propertyScopes[$class] = InternalHydrator::$propertyScopes[$class] ?? InternalHydrator::getPropertyScopes($class);
            foreach ($properties as $name => &$value) {
                [$scope, $name, $readonlyScope] = $propertyScopes[$name] ?? [$class, $name, $class];
                $scopedProperties[$readonlyScope ?? $scope][$name] =& $value;
            }
            unset($value);
        }
        foreach ($scopedProperties as $scope => $properties) {
            if ($properties) {
                (InternalHydrator::$simpleHydrators[$scope] = InternalHydrator::$simpleHydrators[$scope] ?? InternalHydrator::getSimpleHydrator($scope))($properties, $instance);
            }
        }
        return $instance;
    }
}
