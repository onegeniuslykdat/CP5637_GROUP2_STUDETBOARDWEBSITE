<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap\Traits;

use DOMDocument;
use Staatic\Vendor\DOMWrap\NodeList;
trait NodeTrait
{
    public function collection(): NodeList
    {
        return $this->newNodeList([$this]);
    }
    public function document(): ?DOMDocument
    {
        if ($this->isRemoved()) {
            return null;
        }
        return $this->ownerDocument;
    }
    /**
     * @param NodeList $nodeList
     */
    public function result($nodeList)
    {
        if ($nodeList->count()) {
            return $nodeList->first();
        }
        return null;
    }
}
