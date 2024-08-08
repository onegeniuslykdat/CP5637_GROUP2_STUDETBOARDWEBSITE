<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class ModuleCollection implements IteratorAggregate
{
    /**
     * @var ModuleInterface[]
     */
    private $modules;

    /**
     * @param Traversable|ModuleInterface[] $modules
     */
    public function __construct(iterable $modules)
    {
        $this->modules = iterator_to_array($modules);
    }

    /**
     * @return ArrayIterator|ModuleInterface[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->modules);
    }
}
