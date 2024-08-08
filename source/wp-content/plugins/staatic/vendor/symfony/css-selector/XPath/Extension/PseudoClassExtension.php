<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Closure;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class PseudoClassExtension extends AbstractExtension
{
    public function getPseudoClassTranslators(): array
    {
        return ['root' => Closure::fromCallable([$this, 'translateRoot']), 'scope' => Closure::fromCallable([$this, 'translateScopePseudo']), 'first-child' => Closure::fromCallable([$this, 'translateFirstChild']), 'last-child' => Closure::fromCallable([$this, 'translateLastChild']), 'first-of-type' => Closure::fromCallable([$this, 'translateFirstOfType']), 'last-of-type' => Closure::fromCallable([$this, 'translateLastOfType']), 'only-child' => Closure::fromCallable([$this, 'translateOnlyChild']), 'only-of-type' => Closure::fromCallable([$this, 'translateOnlyOfType']), 'empty' => Closure::fromCallable([$this, 'translateEmpty'])];
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateRoot($xpath): XPathExpr
    {
        return $xpath->addCondition('not(parent::*)');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateScopePseudo($xpath): XPathExpr
    {
        return $xpath->addCondition('1');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateFirstChild($xpath): XPathExpr
    {
        return $xpath->addStarPrefix()->addNameTest()->addCondition('position() = 1');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateLastChild($xpath): XPathExpr
    {
        return $xpath->addStarPrefix()->addNameTest()->addCondition('position() = last()');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateFirstOfType($xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:first-of-type" is not implemented.');
        }
        return $xpath->addStarPrefix()->addCondition('position() = 1');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateLastOfType($xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:last-of-type" is not implemented.');
        }
        return $xpath->addStarPrefix()->addCondition('position() = last()');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateOnlyChild($xpath): XPathExpr
    {
        return $xpath->addStarPrefix()->addNameTest()->addCondition('last() = 1');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateOnlyOfType($xpath): XPathExpr
    {
        $element = $xpath->getElement();
        return $xpath->addCondition(sprintf('count(preceding-sibling::%s)=0 and count(following-sibling::%s)=0', $element, $element));
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateEmpty($xpath): XPathExpr
    {
        return $xpath->addCondition('not(*) and not(string-length())');
    }
    public function getName(): string
    {
        return 'pseudo-class';
    }
}
