<?php

namespace Staatic\Vendor\voku\helper;

use ArrayIterator;
interface SimpleHtmlAttributesInterface
{
    /**
     * @param string ...$tokens
     */
    public function add(...$tokens);
    /**
     * @param string $token
     */
    public function contains($token): bool;
    public function entries(): ArrayIterator;
    /**
     * @param int $index
     */
    public function item($index);
    /**
     * @param string ...$tokens
     */
    public function remove(...$tokens);
    /**
     * @param string $old
     * @param string $new
     */
    public function replace($old, $new);
    /**
     * @param string $token
     * @param bool|null $force
     */
    public function toggle($token, $force = null): bool;
}
