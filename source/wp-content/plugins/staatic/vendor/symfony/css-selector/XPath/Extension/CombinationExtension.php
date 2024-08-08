<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Closure;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class CombinationExtension extends AbstractExtension
{
    public function getCombinationTranslators(): array
    {
        return [' ' => Closure::fromCallable([$this, 'translateDescendant']), '>' => Closure::fromCallable([$this, 'translateChild']), '+' => Closure::fromCallable([$this, 'translateDirectAdjacent']), '~' => Closure::fromCallable([$this, 'translateIndirectAdjacent'])];
    }
    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     */
    public function translateDescendant($xpath, $combinedXpath): XPathExpr
    {
        return $xpath->join('/descendant-or-self::*/', $combinedXpath);
    }
    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     */
    public function translateChild($xpath, $combinedXpath): XPathExpr
    {
        return $xpath->join('/', $combinedXpath);
    }
    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     */
    public function translateDirectAdjacent($xpath, $combinedXpath): XPathExpr
    {
        return $xpath->join('/following-sibling::', $combinedXpath)->addNameTest()->addCondition('position() = 1');
    }
    /**
     * @param XPathExpr $xpath
     * @param XPathExpr $combinedXpath
     */
    public function translateIndirectAdjacent($xpath, $combinedXpath): XPathExpr
    {
        return $xpath->join('/following-sibling::', $combinedXpath);
    }
    public function getName(): string
    {
        return 'combination';
    }
}
