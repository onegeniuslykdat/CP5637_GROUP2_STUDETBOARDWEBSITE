<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\VariableNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\BooleanNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
class BooleanNodeDefinition extends ScalarNodeDefinition
{
    public function __construct(?string $name, ?NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);
        $this->nullEquivalent = \true;
    }
    protected function instantiateNode(): VariableNode
    {
        return new BooleanNode($this->name, $this->parent, $this->pathSeparator);
    }
    /**
     * @return static
     */
    public function cannotBeEmpty()
    {
        throw new InvalidDefinitionException('->cannotBeEmpty() is not applicable to BooleanNodeDefinition.');
    }
}
