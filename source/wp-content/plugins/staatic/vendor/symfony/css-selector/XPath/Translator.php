<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath;

use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\NodeExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\CombinationExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\PseudoClassExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\AttributeMatchingExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\ExtensionInterface;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\FunctionNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\NodeInterface;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Parser;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class Translator implements TranslatorInterface
{
    /**
     * @var ParserInterface
     */
    private $mainParser;
    /**
     * @var mixed[]
     */
    private $shortcutParsers = [];
    /**
     * @var mixed[]
     */
    private $extensions = [];
    /**
     * @var mixed[]
     */
    private $nodeTranslators = [];
    /**
     * @var mixed[]
     */
    private $combinationTranslators = [];
    /**
     * @var mixed[]
     */
    private $functionTranslators = [];
    /**
     * @var mixed[]
     */
    private $pseudoClassTranslators = [];
    /**
     * @var mixed[]
     */
    private $attributeMatchingTranslators = [];
    public function __construct(?ParserInterface $parser = null)
    {
        $this->mainParser = $parser ?? new Parser();
        $this->registerExtension(new NodeExtension())->registerExtension(new CombinationExtension())->registerExtension(new FunctionExtension())->registerExtension(new PseudoClassExtension())->registerExtension(new AttributeMatchingExtension());
    }
    /**
     * @param string $element
     */
    public static function getXpathLiteral($element): string
    {
        if (strpos($element, "'") === false) {
            return "'" . $element . "'";
        }
        if (strpos($element, '"') === false) {
            return '"' . $element . '"';
        }
        $string = $element;
        $parts = [];
        while (\true) {
            if (\false !== $pos = strpos($string, "'")) {
                $parts[] = sprintf("'%s'", substr($string, 0, $pos));
                $parts[] = "\"'\"";
                $string = substr($string, $pos + 1);
            } else {
                $parts[] = "'{$string}'";
                break;
            }
        }
        return sprintf('concat(%s)', implode(', ', $parts));
    }
    /**
     * @param string $cssExpr
     * @param string $prefix
     */
    public function cssToXPath($cssExpr, $prefix = 'descendant-or-self::'): string
    {
        $selectors = $this->parseSelectors($cssExpr);
        foreach ($selectors as $index => $selector) {
            if (null !== $selector->getPseudoElement()) {
                throw new ExpressionErrorException('Pseudo-elements are not supported.');
            }
            $selectors[$index] = $this->selectorToXPath($selector, $prefix);
        }
        return implode(' | ', $selectors);
    }
    /**
     * @param SelectorNode $selector
     * @param string $prefix
     */
    public function selectorToXPath($selector, $prefix = 'descendant-or-self::'): string
    {
        return ($prefix ?: '') . $this->nodeToXPath($selector);
    }
    /**
     * @param ExtensionInterface $extension
     * @return static
     */
    public function registerExtension($extension)
    {
        $this->extensions[$extension->getName()] = $extension;
        $this->nodeTranslators = array_merge($this->nodeTranslators, $extension->getNodeTranslators());
        $this->combinationTranslators = array_merge($this->combinationTranslators, $extension->getCombinationTranslators());
        $this->functionTranslators = array_merge($this->functionTranslators, $extension->getFunctionTranslators());
        $this->pseudoClassTranslators = array_merge($this->pseudoClassTranslators, $extension->getPseudoClassTranslators());
        $this->attributeMatchingTranslators = array_merge($this->attributeMatchingTranslators, $extension->getAttributeMatchingTranslators());
        return $this;
    }
    /**
     * @param string $name
     */
    public function getExtension($name): ExtensionInterface
    {
        if (!isset($this->extensions[$name])) {
            throw new ExpressionErrorException(sprintf('Extension "%s" not registered.', $name));
        }
        return $this->extensions[$name];
    }
    /**
     * @param ParserInterface $shortcut
     * @return static
     */
    public function registerParserShortcut($shortcut)
    {
        $this->shortcutParsers[] = $shortcut;
        return $this;
    }
    /**
     * @param NodeInterface $node
     */
    public function nodeToXPath($node): XPathExpr
    {
        if (!isset($this->nodeTranslators[$node->getNodeName()])) {
            throw new ExpressionErrorException(sprintf('Node "%s" not supported.', $node->getNodeName()));
        }
        return $this->nodeTranslators[$node->getNodeName()]($node, $this);
    }
    /**
     * @param string $combiner
     * @param NodeInterface $xpath
     * @param NodeInterface $combinedXpath
     */
    public function addCombination($combiner, $xpath, $combinedXpath): XPathExpr
    {
        if (!isset($this->combinationTranslators[$combiner])) {
            throw new ExpressionErrorException(sprintf('Combiner "%s" not supported.', $combiner));
        }
        return $this->combinationTranslators[$combiner]($this->nodeToXPath($xpath), $this->nodeToXPath($combinedXpath));
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function addFunction($xpath, $function): XPathExpr
    {
        if (!isset($this->functionTranslators[$function->getName()])) {
            throw new ExpressionErrorException(sprintf('Function "%s" not supported.', $function->getName()));
        }
        return $this->functionTranslators[$function->getName()]($xpath, $function);
    }
    /**
     * @param XPathExpr $xpath
     * @param string $pseudoClass
     */
    public function addPseudoClass($xpath, $pseudoClass): XPathExpr
    {
        if (!isset($this->pseudoClassTranslators[$pseudoClass])) {
            throw new ExpressionErrorException(sprintf('Pseudo-class "%s" not supported.', $pseudoClass));
        }
        return $this->pseudoClassTranslators[$pseudoClass]($xpath);
    }
    /**
     * @param XPathExpr $xpath
     * @param string $operator
     * @param string $attribute
     * @param string|null $value
     */
    public function addAttributeMatching($xpath, $operator, $attribute, $value): XPathExpr
    {
        if (!isset($this->attributeMatchingTranslators[$operator])) {
            throw new ExpressionErrorException(sprintf('Attribute matcher operator "%s" not supported.', $operator));
        }
        return $this->attributeMatchingTranslators[$operator]($xpath, $attribute, $value);
    }
    private function parseSelectors(string $css): array
    {
        foreach ($this->shortcutParsers as $shortcut) {
            $tokens = $shortcut->parse($css);
            if ($tokens) {
                return $tokens;
            }
        }
        return $this->mainParser->parse($css);
    }
}
