<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
interface ArrayInterface extends ArrayAccess, Countable, IteratorAggregate
{
    public function clear(): void;
    public function toArray(): array;
    public function isEmpty(): bool;
}
