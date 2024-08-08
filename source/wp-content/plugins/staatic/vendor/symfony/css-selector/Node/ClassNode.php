<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class ClassNode extends AbstractNode
{
    /**
     * @var NodeInterface
     */
    private $selector;
    /**
     * @var string
     */
    private $name;
    public function __construct(NodeInterface $selector, string $name)
    {
        $this->selector = $selector;
        $this->name = $name;
    }
    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }
    public function __toString(): string
    {
        return sprintf('%s[%s.%s]', $this->getNodeName(), $this->selector, $this->name);
    }
}
