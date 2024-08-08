<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
interface ParserInterface
{
    /**
     * @param string $source
     */
    public function parse($source): array;
}
