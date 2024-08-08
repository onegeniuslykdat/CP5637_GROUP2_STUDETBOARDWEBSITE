<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class AttributeNode extends AbstractNode
{
    /**
     * @var NodeInterface
     */
    private $selector;
    /**
     * @var string|null
     */
    private $namespace;
    /**
     * @var string
     */
    private $attribute;
    /**
     * @var string
     */
    private $operator;
    /**
     * @var string|null
     */
    private $value;
    public function __construct(NodeInterface $selector, ?string $namespace, string $attribute, string $operator, ?string $value)
    {
        $this->selector = $selector;
        $this->namespace = $namespace;
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
    }
    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }
    public function getAttribute(): string
    {
        return $this->attribute;
    }
    public function getOperator(): string
    {
        return $this->operator;
    }
    public function getValue(): ?string
    {
        return $this->value;
    }
    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }
    public function __toString(): string
    {
        $attribute = $this->namespace ? $this->namespace . '|' . $this->attribute : $this->attribute;
        return ('exists' === $this->operator) ? sprintf('%s[%s[%s]]', $this->getNodeName(), $this->selector, $attribute) : sprintf("%s[%s[%s %s '%s']]", $this->getNodeName(), $this->selector, $attribute, $this->operator, $this->value);
    }
}
