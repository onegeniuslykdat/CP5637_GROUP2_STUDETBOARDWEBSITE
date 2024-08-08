<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use ReturnTypeWillChange;
use ArrayIterator;
use Traversable;
use function count;
abstract class AbstractArray implements ArrayInterface
{
    /**
     * @var mixed[]
     */
    protected $data = [];
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }
    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }
    /**
     * @param mixed $offset
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
    public function __serialize(): array
    {
        return $this->data;
    }
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }
    public function count(): int
    {
        return count($this->data);
    }
    public function clear(): void
    {
        $this->data = [];
    }
    public function toArray(): array
    {
        return $this->data;
    }
    public function isEmpty(): bool
    {
        return $this->data === [];
    }
}
