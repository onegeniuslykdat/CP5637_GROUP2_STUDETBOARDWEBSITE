<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\CollectionMismatchException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use Staatic\Vendor\Ramsey\Collection\Exception\UnsupportedOperationException;
interface CollectionInterface extends ArrayInterface
{
    /**
     * @param mixed $element
     */
    public function add($element): bool;
    /**
     * @param mixed $element
     * @param bool $strict
     */
    public function contains($element, $strict = \true): bool;
    public function getType(): string;
    /**
     * @param mixed $element
     */
    public function remove($element): bool;
    /**
     * @param string $propertyOrMethod
     */
    public function column($propertyOrMethod): array;
    /**
     * @return mixed
     */
    public function first();
    /**
     * @return mixed
     */
    public function last();
    /**
     * @param string|null $propertyOrMethod
     * @param Sort $order
     */
    public function sort($propertyOrMethod = null, $order = Sort::Ascending): self;
    /**
     * @param callable $callback
     */
    public function filter($callback): self;
    /**
     * @param string|null $propertyOrMethod
     * @param mixed $value
     */
    public function where($propertyOrMethod, $value): self;
    /**
     * @param callable $callback
     */
    public function map($callback): self;
    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce($callback, $initial);
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface $other
     */
    public function diff($other): self;
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface $other
     */
    public function intersect($other): self;
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface ...$collections
     */
    public function merge(...$collections): self;
}
