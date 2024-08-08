<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\ElementNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class ElementParser implements ParserInterface
{
    /**
     * @param string $source
     */
    public function parse($source): array
    {
        if (preg_match('/^(?:([a-z]++)\|)?([\w-]++|\*)$/i', trim($source), $matches)) {
            return [new SelectorNode(new ElementNode($matches[1] ?: null, $matches[2]))];
        }
        return [];
    }
}
