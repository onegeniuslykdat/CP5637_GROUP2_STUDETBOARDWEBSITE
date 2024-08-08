<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
class ChildDefinition extends Definition
{
    /**
     * @var string
     */
    private $parent;
    public function __construct(string $parent)
    {
        $this->parent = $parent;
    }
    public function getParent(): string
    {
        return $this->parent;
    }
    /**
     * @param string $parent
     * @return static
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
    /**
     * @param int|string $index
     * @return mixed
     */
    public function getArgument($index)
    {
        if (\array_key_exists('index_' . $index, $this->arguments)) {
            return $this->arguments['index_' . $index];
        }
        return parent::getArgument($index);
    }
    /**
     * @param int|string $index
     * @param mixed $value
     * @return static
     */
    public function replaceArgument($index, $value)
    {
        if (\is_int($index)) {
            $this->arguments['index_' . $index] = $value;
        } elseif (strncmp($index, '$', strlen('$')) === 0) {
            $this->arguments[$index] = $value;
        } else {
            throw new InvalidArgumentException('The argument must be an existing index or the name of a constructor\'s parameter.');
        }
        return $this;
    }
}
