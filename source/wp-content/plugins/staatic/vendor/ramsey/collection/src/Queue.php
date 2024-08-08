<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
use function array_key_first;
class Queue extends AbstractArray implements QueueInterface
{
    /**
     * @readonly
     * @var string
     */
    private $queueType;
    use TypeTrait;
    use ValueToStringTrait;
    public function __construct(string $queueType, array $data = [])
    {
        $this->queueType = $queueType;
        parent::__construct($data);
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
        $this->data[] = $value;
    }
    /**
     * @param mixed $element
     */
    public function add($element): bool
    {
        $this[] = $element;
        return \true;
    }
    /**
     * @return mixed
     */
    public function element()
    {
        if ($this->peek() !== null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $this->peek();
    }
    /**
     * @param mixed $element
     */
    public function offer($element): bool
    {
        try {
            return $this->add($element);
        } catch (InvalidArgumentException $exception) {
            return \false;
        }
    }
    /**
     * @return mixed
     */
    public function peek()
    {
        reset($this->data);
        $index = key($this->data);
        if ($index === null) {
            return null;
        }
        return $this[$index];
    }
    /**
     * @return mixed
     */
    public function poll()
    {
        reset($this->data);
        $index = key($this->data);
        if ($index === null) {
            return null;
        }
        $head = $this[$index];
        unset($this[$index]);
        return $head;
    }
    /**
     * @return mixed
     */
    public function remove()
    {
        if ($this->poll() !== null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $this->poll();
    }
    public function getType(): string
    {
        return $this->queueType;
    }
}
