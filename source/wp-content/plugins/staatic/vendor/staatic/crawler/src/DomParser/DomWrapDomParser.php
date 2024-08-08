<?php

namespace Staatic\Crawler\DomParser;

use Staatic\Vendor\DOMWrap\Document;
use Staatic\Vendor\DOMWrap\ProcessingInstruction;
final class DomWrapDomParser implements DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html)
    {
        $document = new Document();
        $document->loadHTML($html, \LIBXML_NOERROR);
        return $document;
    }
    public function getHtml($document): string
    {
        $document->contents()->each(function ($node) {
            if ($node instanceof ProcessingInstruction && $node->nodeName == 'xml') {
                $node->destroy();
            }
        });
        return $document->saveHTML();
    }
    /**
     * @param string $name
     */
    public function hasAttribute($element, $name): bool
    {
        return $element->hasAttribute($name);
    }
    /**
     * @param string $name
     */
    public function getAttribute($element, $name): string
    {
        return $element->getAttribute($name);
    }
    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($element, $name, $value): void
    {
        $element->setAttribute($name, $value);
    }
    /**
     * @param string $name
     */
    public function removeAttribute($element, $name): void
    {
        $element->removeAttribute($name);
    }
    public function getText($element): string
    {
        return $element->textContent;
    }
    /**
     * @param string $value
     */
    public function setText($element, $value): void
    {
        $element->textContent = $value;
    }
}
