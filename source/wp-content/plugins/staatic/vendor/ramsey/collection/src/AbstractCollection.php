<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Closure;
use Staatic\Vendor\Ramsey\Collection\Exception\CollectionMismatchException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use Staatic\Vendor\Ramsey\Collection\Exception\UnsupportedOperationException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueExtractorTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
use function array_filter;
use function array_key_first;
use function array_key_last;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_search;
use function array_udiff;
use function array_uintersect;
use function in_array;
use function is_int;
use function is_object;
use function spl_object_id;
use function sprintf;
use function usort;
abstract class AbstractCollection extends AbstractArray implements CollectionInterface
{
    use TypeTrait;
    use ValueToStringTrait;
    use ValueExtractorTrait;
    /**
     * @param mixed $element
     */
    public function add($element): bool
    {
        $this[] = $element;
        return \true;
    }
    /**
     * @param mixed $element
     * @param bool $strict
     */
    public function contains($element, $strict = \true): bool
    {
        return in_array($element, $this->data, $strict);
    }
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->checkType($this->getType(), $value) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($value));
        }
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    /**
     * @param mixed $element
     */
    public function remove($element): bool
    {
        if (($position = array_search($element, $this->data, \true)) !== \false) {
            unset($this[$position]);
            return \true;
        }
        return \false;
    }
    /**
     * @param string $propertyOrMethod
     */
    public function column($propertyOrMethod): array
    {
        $temp = [];
        foreach ($this->data as $item) {
            $temp[] = $this->extractValue($item, $propertyOrMethod);
        }
        return $temp;
    }
    /**
     * @return mixed
     */
    public function first()
    {
        reset($this->data);
        $firstIndex = key($this->data);
        if ($firstIndex === null) {
            throw new NoSuchElementException('Can\'t determine first item. Collection is empty');
        }
        return $this->data[$firstIndex];
    }
    /**
     * @return mixed
     */
    public function last()
    {
        end($this->data);
        $lastIndex = key($this->data);
        reset($this->data);
        if ($lastIndex === null) {
            throw new NoSuchElementException('Can\'t determine last item. Collection is empty');
        }
        return $this->data[$lastIndex];
    }
    /**
     * @param string|null $propertyOrMethod
     * @param Sort $order
     */
    public function sort($propertyOrMethod = null, $order = Sort::Ascending): CollectionInterface
    {
        $collection = clone $this;
        usort($collection->data, function ($a, $b) use ($propertyOrMethod, $order): int {
            $aValue = $this->extractValue($a, $propertyOrMethod);
            $bValue = $this->extractValue($b, $propertyOrMethod);
            return ($aValue <=> $bValue) * (($order === Sort::Descending) ? -1 : 1);
        });
        return $collection;
    }
    /**
     * @param callable $callback
     */
    public function filter($callback): CollectionInterface
    {
        $collection = clone $this;
        $collection->data = array_merge([], array_filter($collection->data, $callback === null ? function ($value, $key) : bool {
            return !empty($value);
        } : $callback, $callback === null ? ARRAY_FILTER_USE_BOTH : 0));
        return $collection;
    }
    /**
     * @param string|null $propertyOrMethod
     * @param mixed $value
     */
    public function where($propertyOrMethod, $value): CollectionInterface
    {
        return $this->filter(function ($item) use ($propertyOrMethod, $value): bool {
            $accessorValue = $this->extractValue($item, $propertyOrMethod);
            return $accessorValue === $value;
        });
    }
    /**
     * @param callable $callback
     */
    public function map($callback): CollectionInterface
    {
        return new Collection('mixed', array_map($callback, $this->data));
    }
    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce($callback, $initial)
    {
        return array_reduce($this->data, $callback, $initial);
    }
    /**
     * @param CollectionInterface $other
     */
    public function diff($other): CollectionInterface
    {
        $this->compareCollectionTypes($other);
        $diffAtoB = array_udiff($this->data, $other->toArray(), $this->getComparator());
        $diffBtoA = array_udiff($other->toArray(), $this->data, $this->getComparator());
        $diff = array_merge($diffAtoB, $diffBtoA);
        $collection = clone $this;
        $collection->data = $diff;
        return $collection;
    }
    /**
     * @param CollectionInterface $other
     */
    public function intersect($other): CollectionInterface
    {
        $this->compareCollectionTypes($other);
        $intersect = array_uintersect($this->data, $other->toArray(), $this->getComparator());
        $collection = clone $this;
        $collection->data = $intersect;
        return $collection;
    }
    /**
     * @param CollectionInterface ...$collections
     */
    public function merge(...$collections): CollectionInterface
    {
        $mergedCollection = clone $this;
        foreach ($collections as $index => $collection) {
            if (!$collection instanceof static) {
                throw new CollectionMismatchException(sprintf('Collection with index %d must be of type %s', $index, static::class));
            }
            if ($this->getUniformType($collection) !== $this->getUniformType($this)) {
                throw new CollectionMismatchException(sprintf('Collection items in collection with index %d must be of type %s', $index, $this->getType()));
            }
            foreach ($collection as $key => $value) {
                if (is_int($key)) {
                    $mergedCollection[] = $value;
                } else {
                    $mergedCollection[$key] = $value;
                }
            }
        }
        return $mergedCollection;
    }
    private function compareCollectionTypes(CollectionInterface $other): void
    {
        if (!$other instanceof static) {
            throw new CollectionMismatchException('Collection must be of type ' . static::class);
        }
        if ($this->getUniformType($other) !== $this->getUniformType($this)) {
            throw new CollectionMismatchException('Collection items must be of type ' . $this->getType());
        }
    }
    private function getComparator(): Closure
    {
        return function ($a, $b): int {
            if (is_object($a) && is_object($b)) {
                $a = spl_object_id($a);
                $b = spl_object_id($b);
            }
            return ($a === $b) ? 0 : (($a < $b) ? 1 : -1);
        };
    }
    private function getUniformType(CollectionInterface $collection): string
    {
        switch ($collection->getType()) {
            case 'integer':
                return 'int';
            case 'boolean':
                return 'bool';
            case 'double':
                return 'float';
            default:
                return $collection->getType();
        }
    }
}
