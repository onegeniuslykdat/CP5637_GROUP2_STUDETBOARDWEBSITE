<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap\Traits;

use DOMDocument;
use Staatic\Vendor\DOMWrap\NodeList;
trait CommonTrait
{
    abstract public function collection(): NodeList;
    abstract public function document(): ?DOMDocument;
    /**
     * @param NodeList $nodeList
     */
    abstract public function result($nodeList);
    public function isRemoved(): bool
    {
        return !isset($this->nodeType);
    }
}
