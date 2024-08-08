<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use function array_key_last;
use function array_pop;
use function array_unshift;
class DoubleEndedQueue extends Queue implements DoubleEndedQueueInterface
{
    /**
     * @readonly
     * @var string
     */
    private $queueType;
    public function __construct(string $queueType, array $data = [])
    {
        $this->queueType = $queueType;
        parent::__construct($this->queueType, $data);
    }
    /**
     * @param mixed $element
     */
    public function addFirst($element): bool
    {
        if ($this->checkType($this->getType(), $element) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($element));
        }
        array_unshift($this->data, $element);
        return \true;
    }
    /**
     * @param mixed $element
     */
    public function addLast($element): bool
    {
        return $this->add($element);
    }
    /**
     * @param mixed $element
     */
    public function offerFirst($element): bool
    {
        try {
            return $this->addFirst($element);
        } catch (InvalidArgumentException $exception) {
            return \false;
        }
    }
    /**
     * @param mixed $element
     */
    public function offerLast($element): bool
    {
        return $this->offer($element);
    }
    /**
     * @return mixed
     */
    public function removeFirst()
    {
        return $this->remove();
    }
    /**
     * @return mixed
     */
    public function removeLast()
    {
        if ($this->pollLast() !== null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $this->pollLast();
    }
    /**
     * @return mixed
     */
    public function pollFirst()
    {
        return $this->poll();
    }
    /**
     * @return mixed
     */
    public function pollLast()
    {
        return array_pop($this->data);
    }
    /**
     * @return mixed
     */
    public function firstElement()
    {
        return $this->element();
    }
    /**
     * @return mixed
     */
    public function lastElement()
    {
        if ($this->peekLast() !== null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $this->peekLast();
    }
    /**
     * @return mixed
     */
    public function peekFirst()
    {
        return $this->peek();
    }
    /**
     * @return mixed
     */
    public function peekLast()
    {
        end($this->data);
        $lastIndex = key($this->data);
        reset($this->data);
        if ($lastIndex === null) {
            return null;
        }
        return $this->data[$lastIndex];
    }
}
