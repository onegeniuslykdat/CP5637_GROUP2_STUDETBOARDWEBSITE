<?php

namespace Staatic\Vendor\Psr\Cache;

interface CacheItemPoolInterface
{
    /**
     * @param string $key
     */
    public function getItem($key): CacheItemInterface;
    /**
     * @param mixed[] $keys
     */
    public function getItems($keys = []): iterable;
    /**
     * @param string $key
     */
    public function hasItem($key): bool;
    public function clear(): bool;
    /**
     * @param string $key
     */
    public function deleteItem($key): bool;
    /**
     * @param mixed[] $keys
     */
    public function deleteItems($keys): bool;
    /**
     * @param CacheItemInterface $item
     */
    public function save($item): bool;
    /**
     * @param CacheItemInterface $item
     */
    public function saveDeferred($item): bool;
    public function commit(): bool;
}
