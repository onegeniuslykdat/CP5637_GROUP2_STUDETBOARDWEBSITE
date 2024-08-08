<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\ElementNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class EmptyStringParser implements ParserInterface
{
    /**
     * @param string $source
     */
    public function parse($source): array
    {
        if ('' == $source) {
            return [new SelectorNode(new ElementNode(null, '*'))];
        }
        return [];
    }
}
