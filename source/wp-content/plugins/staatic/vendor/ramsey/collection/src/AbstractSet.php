<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

abstract class AbstractSet extends AbstractCollection
{
    /**
     * @param mixed $element
     */
    public function add($element): bool
    {
        if ($this->contains($element)) {
            return \false;
        }
        return parent::add($element);
    }
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->contains($value)) {
            return;
        }
        parent::offsetSet($offset, $value);
    }
}
