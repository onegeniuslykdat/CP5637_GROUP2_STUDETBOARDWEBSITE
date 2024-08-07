<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
class FunctionNode extends AbstractNode
{
    /**
     * @var NodeInterface
     */
    private $selector;
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed[]
     */
    private $arguments;
    public function __construct(NodeInterface $selector, string $name, array $arguments = [])
    {
        $this->selector = $selector;
        $this->name = strtolower($name);
        $this->arguments = $arguments;
    }
    public function getSelector(): NodeInterface
    {
        return $this->selector;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getArguments(): array
    {
        return $this->arguments;
    }
    public function getSpecificity(): Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }
    public function __toString(): string
    {
        $arguments = implode(', ', array_map(function (Token $token) {
            return "'" . $token->getValue() . "'";
        }, $this->arguments));
        return sprintf('%s[%s:%s(%s)]', $this->getNodeName(), $this->selector, $this->name, $arguments ? '[' . $arguments . ']' : '');
    }
}
