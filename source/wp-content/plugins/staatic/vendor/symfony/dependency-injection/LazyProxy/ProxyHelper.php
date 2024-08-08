<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy;

use ReflectionParameter;
use ReflectionType;
use ReflectionFunctionAbstract;
use ReflectionUnionType;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionMethod;
trigger_deprecation('symfony/dependency-injection', '6.2', 'The "%s" class is deprecated, use "%s" instead.', ProxyHelper::class, \Staatic\Vendor\Symfony\Component\VarExporter\ProxyHelper::class);
class ProxyHelper
{
    /**
     * @param ReflectionFunctionAbstract $r
     * @param ReflectionParameter|null $p
     * @param bool $noBuiltin
     */
    public static function getTypeHint($r, $p = null, $noBuiltin = \false): ?string
    {
        if ($p instanceof ReflectionParameter) {
            $type = $p->getType();
        } else {
            $type = $r->getReturnType();
        }
        if (!$type) {
            return null;
        }
        return self::getTypeHintForType($type, $r, $noBuiltin);
    }
    private static function getTypeHintForType(ReflectionType $type, ReflectionFunctionAbstract $r, bool $noBuiltin): ?string
    {
        $types = [];
        $glue = '|';
        if ($type instanceof ReflectionUnionType) {
            $reflectionTypes = $type->getTypes();
        } elseif ($type instanceof ReflectionIntersectionType) {
            $reflectionTypes = $type->getTypes();
            $glue = '&';
        } elseif ($type instanceof ReflectionNamedType) {
            $reflectionTypes = [$type];
        } else {
            return null;
        }
        foreach ($reflectionTypes as $type) {
            if ($type instanceof ReflectionIntersectionType) {
                $typeHint = self::getTypeHintForType($type, $r, $noBuiltin);
                if (null === $typeHint) {
                    return null;
                }
                $types[] = sprintf('(%s)', $typeHint);
                continue;
            }
            if ($type->isBuiltin()) {
                if (!$noBuiltin) {
                    $types[] = $type->getName();
                }
                continue;
            }
            $lcName = strtolower($type->getName());
            $prefix = $noBuiltin ? '' : '\\';
            if ('self' !== $lcName && 'parent' !== $lcName) {
                $types[] = $prefix . $type->getName();
                continue;
            }
            if (!$r instanceof ReflectionMethod) {
                continue;
            }
            if ('self' === $lcName) {
                $types[] = $prefix . $r->getDeclaringClass()->name;
            } else {
                $types[] = ($parent = $r->getDeclaringClass()->getParentClass()) ? $prefix . $parent->name : null;
            }
        }
        sort($types);
        return $types ? implode($glue, $types) : null;
    }
}
