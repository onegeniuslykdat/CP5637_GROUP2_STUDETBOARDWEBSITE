<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class ElementNode extends AbstractNode
{
    /**
     * @var string|null
     */
    private $namespace;
    /**
     * @var string|null
     */
    private $element;
    public function __construct(?string $namespace = null, ?string $element = null)
    {
        $this->namespace = $namespace;
        $this->element = $element;
    }
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }
    public function getElement(): ?string
    {
        return $this->element;
    }
    public function getSpecificity(): Specificity
    {
        return new Specificity(0, 0, $this->element ? 1 : 0);
    }
    public function __toString(): string
    {
        $element = $this->element ?: '*';
        return sprintf('%s[%s]', $this->getNodeName(), $this->namespace ? $this->namespace . '|' . $element : $element);
    }
}
