<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\AbstractArray;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Traversable;
use function array_key_exists;
use function array_keys;
use function in_array;
use function var_export;
abstract class AbstractMap extends AbstractArray implements MapInterface
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            throw new InvalidArgumentException('Map elements are key/value pairs; a key must be provided for ' . 'value ' . var_export($value, \true));
        }
        $this->data[$offset] = $value;
    }
    /**
     * @param int|string $key
     */
    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->data);
    }
    /**
     * @param mixed $value
     */
    public function containsValue($value): bool
    {
        return in_array($value, $this->data, \true);
    }
    public function keys(): array
    {
        return array_keys($this->data);
    }
    /**
     * @param int|string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        return $this[$key] ?? $defaultValue;
    }
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value)
    {
        $previousValue = $this->get($key);
        $this[$key] = $value;
        return $previousValue;
    }
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function putIfAbsent($key, $value)
    {
        $currentValue = $this->get($key);
        if ($currentValue === null) {
            $this[$key] = $value;
        }
        return $currentValue;
    }
    /**
     * @param int|string $key
     * @return mixed
     */
    public function remove($key)
    {
        $previousValue = $this->get($key);
        unset($this[$key]);
        return $previousValue;
    }
    /**
     * @param int|string $key
     * @param mixed $value
     */
    public function removeIf($key, $value): bool
    {
        if ($this->get($key) === $value) {
            unset($this[$key]);
            return \true;
        }
        return \false;
    }
    /**
     * @param int|string $key
     * @param mixed $value
     * @return mixed
     */
    public function replace($key, $value)
    {
        $currentValue = $this->get($key);
        if ($this->containsKey($key)) {
            $this[$key] = $value;
        }
        return $currentValue;
    }
    /**
     * @param int|string $key
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    public function replaceIf($key, $oldValue, $newValue): bool
    {
        if ($this->get($key) === $oldValue) {
            $this[$key] = $newValue;
            return \true;
        }
        return \false;
    }
    public function __serialize(): array
    {
        return parent::__serialize();
    }
    public function toArray(): array
    {
        return parent::toArray();
    }
}
