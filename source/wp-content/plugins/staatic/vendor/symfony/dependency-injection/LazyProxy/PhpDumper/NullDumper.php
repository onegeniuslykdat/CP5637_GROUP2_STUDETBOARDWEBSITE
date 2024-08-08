<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class NullDumper implements DumperInterface
{
    /**
     * @param Definition $definition
     * @param bool|null $asGhostObject
     * @param string|null $id
     */
    public function isProxyCandidate($definition, &$asGhostObject = null, $id = null): bool
    {
        return $asGhostObject = \false;
    }
    /**
     * @param Definition $definition
     * @param string $id
     * @param string $factoryCode
     */
    public function getProxyFactoryCode($definition, $id, $factoryCode): string
    {
        return '';
    }
    /**
     * @param Definition $definition
     * @param string|null $id
     */
    public function getProxyCode($definition, $id = null): string
    {
        return '';
    }
}
