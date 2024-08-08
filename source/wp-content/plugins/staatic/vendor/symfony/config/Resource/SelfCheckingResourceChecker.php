<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use Staatic\Vendor\Symfony\Component\Config\ResourceCheckerInterface;
class SelfCheckingResourceChecker implements ResourceCheckerInterface
{
    /**
     * @var mixed[]
     */
    private static $cache = [];
    /**
     * @param ResourceInterface $metadata
     */
    public function supports($metadata): bool
    {
        return $metadata instanceof SelfCheckingResourceInterface;
    }
    /**
     * @param ResourceInterface $resource
     * @param int $timestamp
     */
    public function isFresh($resource, $timestamp): bool
    {
        $key = "{$resource}:{$timestamp}";
        return self::$cache[$key] = self::$cache[$key] ?? $resource->isFresh($timestamp);
    }
}
