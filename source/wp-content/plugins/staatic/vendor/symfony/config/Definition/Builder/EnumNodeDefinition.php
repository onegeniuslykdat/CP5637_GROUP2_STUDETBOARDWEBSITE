<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\VariableNode;
use RuntimeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\EnumNode;
class EnumNodeDefinition extends ScalarNodeDefinition
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @param mixed[] $values
     * @return static
     */
    public function values($values)
    {
        if (!$values) {
            throw new InvalidArgumentException('->values() must be called with at least one value.');
        }
        $this->values = $values;
        return $this;
    }
    protected function instantiateNode(): VariableNode
    {
        if (!isset($this->values)) {
            throw new RuntimeException('You must call ->values() on enum nodes.');
        }
        return new EnumNode($this->name, $this->parent, $this->values, $this->pathSeparator);
    }
}
