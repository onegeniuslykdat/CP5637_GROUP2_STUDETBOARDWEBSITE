<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class HashNode extends AbstractNode
{
    /**
     * @var NodeInterface
     */
    private $selector;
    /**
     * @var string
     */
    private $id;
    public function __construct(NodeInterface $selector, string $id)
    {
        $this->selector = $selector;
        $this->id = $id;
    }
    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(1, 0, 0));
    }
    public function __toString(): string
    {
        return sprintf('%s[%s#%s]', $this->getNodeName(), $this->selector, $this->id);
    }
}
