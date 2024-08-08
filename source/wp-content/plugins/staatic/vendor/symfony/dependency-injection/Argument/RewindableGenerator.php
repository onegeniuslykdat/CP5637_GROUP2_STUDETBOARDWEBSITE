<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use IteratorAggregate;
use Countable;
use Closure;
use Traversable;
class RewindableGenerator implements IteratorAggregate, Countable
{
    /**
     * @var Closure
     */
    private $generator;
    /**
     * @var Closure|int
     */
    private $count;
    /**
     * @param int|callable $count
     */
    public function __construct(callable $generator, $count)
    {
        $this->generator = Closure::fromCallable($generator);
        $this->count = \is_int($count) ? $count : Closure::fromCallable($count);
    }
    public function getIterator(): Traversable
    {
        $g = $this->generator;
        return $g();
    }
    public function count(): int
    {
        if (!\is_int($count = $this->count)) {
            $this->count = $count();
        }
        return $this->count;
    }
}
