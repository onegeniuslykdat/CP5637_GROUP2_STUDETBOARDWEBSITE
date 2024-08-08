<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use RuntimeException;
interface QueueInterface extends ArrayInterface
{
    /**
     * @param mixed $element
     */
    public function add($element): bool;
    /**
     * @return mixed
     */
    public function element();
    /**
     * @param mixed $element
     */
    public function offer($element): bool;
    /**
     * @return mixed
     */
    public function peek();
    /**
     * @return mixed
     */
    public function poll();
    /**
     * @return mixed
     */
    public function remove();
    public function getType(): string;
}
