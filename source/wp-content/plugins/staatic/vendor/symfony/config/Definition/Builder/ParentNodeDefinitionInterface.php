<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

interface ParentNodeDefinitionInterface extends BuilderAwareInterface
{
    public function children(): NodeBuilder;
    /**
     * @param NodeDefinition $node
     * @return static
     */
    public function append($node);
    public function getChildNodeDefinitions(): array;
}
