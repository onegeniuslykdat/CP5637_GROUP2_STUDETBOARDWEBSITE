<?php

namespace Staatic\Vendor\Psr\Cache;

use DateTimeInterface;
use DateInterval;
interface CacheItemInterface
{
    public function getKey(): string;
    /**
     * @return mixed
     */
    public function get();
    public function isHit(): bool;
    /**
     * @param mixed $value
     * @return static
     */
    public function set($value);
    /**
     * @param DateTimeInterface|null $expiration
     * @return static
     */
    public function expiresAt($expiration);
    /**
     * @param int|DateInterval|null $time
     * @return static
     */
    public function expiresAfter($time);
}
