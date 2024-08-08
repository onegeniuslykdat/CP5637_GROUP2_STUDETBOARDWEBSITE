<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

abstract class AbstractNode implements NodeInterface
{
    /**
     * @var string
     */
    private $nodeName;
    public function getNodeName(): string
    {
        return $this->nodeName = $this->nodeName ?? preg_replace('~.*\\\\([^\\\\]+)Node$~', '$1', static::class);
    }
}
