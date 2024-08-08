<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
class TreeBuilder implements NodeParentInterface
{
    protected $tree;
    protected $root;
    public function __construct(string $name, string $type = 'array', ?NodeBuilder $builder = null)
    {
        $builder = $builder ?? new NodeBuilder();
        $this->root = $builder->node($name, $type)->setParent($this);
    }
    /**
     * @return NodeDefinition|ArrayNodeDefinition
     */
    public function getRootNode()
    {
        return $this->root;
    }
    public function buildTree(): NodeInterface
    {
        return $this->tree = $this->tree ?? $this->root->getNode(\true);
    }
    /**
     * @param string $separator
     */
    public function setPathSeparator($separator)
    {
        $this->tree = null;
        $this->root->setPathSeparator($separator);
    }
}
