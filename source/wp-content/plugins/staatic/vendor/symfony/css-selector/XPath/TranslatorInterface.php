<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
interface TranslatorInterface
{
    /**
     * @param string $cssExpr
     * @param string $prefix
     */
    public function cssToXPath($cssExpr, $prefix = 'descendant-or-self::'): string;
    /**
     * @param SelectorNode $selector
     * @param string $prefix
     */
    public function selectorToXPath($selector, $prefix = 'descendant-or-self::'): string;
}
