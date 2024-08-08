<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

class MergeBuilder
{
    protected $node;
    public $allowFalse = \false;
    public $allowOverwrite = \true;
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @param bool $allow
     * @return static
     */
    public function allowUnset($allow = \true)
    {
        $this->allowFalse = $allow;
        return $this;
    }
    /**
     * @param bool $deny
     * @return static
     */
    public function denyOverwrite($deny = \true)
    {
        $this->allowOverwrite = !$deny;
        return $this;
    }
    /**
     * @return NodeDefinition|ArrayNodeDefinition|VariableNodeDefinition
     */
    public function end()
    {
        return $this->node;
    }
}
