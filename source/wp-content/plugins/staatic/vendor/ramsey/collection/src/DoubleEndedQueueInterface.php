<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use RuntimeException;
interface DoubleEndedQueueInterface extends QueueInterface
{
    /**
     * @param mixed $element
     */
    public function addFirst($element): bool;
    /**
     * @param mixed $element
     */
    public function addLast($element): bool;
    /**
     * @param mixed $element
     */
    public function offerFirst($element): bool;
    /**
     * @param mixed $element
     */
    public function offerLast($element): bool;
    /**
     * @return mixed
     */
    public function removeFirst();
    /**
     * @return mixed
     */
    public function removeLast();
    /**
     * @return mixed
     */
    public function pollFirst();
    /**
     * @return mixed
     */
    public function pollLast();
    /**
     * @return mixed
     */
    public function firstElement();
    /**
     * @return mixed
     */
    public function lastElement();
    /**
     * @return mixed
     */
    public function peekFirst();
    /**
     * @return mixed
     */
    public function peekLast();
}
