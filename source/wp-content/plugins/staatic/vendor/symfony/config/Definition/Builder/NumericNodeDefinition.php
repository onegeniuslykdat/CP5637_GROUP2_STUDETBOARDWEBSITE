<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
abstract class NumericNodeDefinition extends ScalarNodeDefinition
{
    protected $min;
    protected $max;
    /**
     * @param int|float $max
     * @return static
     */
    public function max($max)
    {
        if (isset($this->min) && $this->min > $max) {
            throw new InvalidArgumentException(sprintf('You cannot define a max(%s) as you already have a min(%s).', $max, $this->min));
        }
        $this->max = $max;
        return $this;
    }
    /**
     * @param int|float $min
     * @return static
     */
    public function min($min)
    {
        if (isset($this->max) && $this->max < $min) {
            throw new InvalidArgumentException(sprintf('You cannot define a min(%s) as you already have a max(%s).', $min, $this->max));
        }
        $this->min = $min;
        return $this;
    }
    /**
     * @return static
     */
    public function cannotBeEmpty()
    {
        throw new InvalidDefinitionException('->cannotBeEmpty() is not applicable to NumericNodeDefinition.');
    }
}
