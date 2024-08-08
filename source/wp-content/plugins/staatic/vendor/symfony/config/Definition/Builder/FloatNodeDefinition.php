<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\VariableNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\FloatNode;
class FloatNodeDefinition extends NumericNodeDefinition
{
    protected function instantiateNode(): VariableNode
    {
        return new FloatNode($this->name, $this->parent, $this->min, $this->max, $this->pathSeparator);
    }
}
