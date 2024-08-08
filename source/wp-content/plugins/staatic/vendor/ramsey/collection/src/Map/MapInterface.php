<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\ArrayInterface;
interface MapInterface extends ArrayInterface
{
    /**
     * @param int|string $key
     */
    public function containsKey($key): bool;
    /**
     * @param mixed $value
     */
    public function containsValue($value): bool;
    public function keys(): array;
    /**
     * @param int|string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null);
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value);
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function putIfAbsent($key, $value);
    /**
     * @param int|string $key
     * @return mixed
     */
    public function remove($key);
    /**
     * @param int|string $key
     * @param mixed $value
     */
    public function removeIf($key, $value): bool;
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function replace($key, $value);
    /**
     * @param int|string $key
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    public function replaceIf($key, $oldValue, $newValue): bool;
}
