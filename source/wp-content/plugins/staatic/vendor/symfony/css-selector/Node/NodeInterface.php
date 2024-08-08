<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

use Stringable;
interface NodeInterface extends Stringable
{
    public function getNodeName(): string;
    public function getSpecificity(): Specificity;
}
