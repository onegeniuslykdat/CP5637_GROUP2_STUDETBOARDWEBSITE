<?php

namespace Staatic\Crawler\DomParser;

use DOMDocument;
use DOMElement;
interface DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html);
    public function getHtml($document): string;
    /**
     * @param string $name
     */
    public function hasAttribute($element, $name): bool;
    /**
     * @param string $name
     */
    public function getAttribute($element, $name): string;
    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($element, $name, $value): void;
    /**
     * @param string $name
     */
    public function removeAttribute($element, $name): void;
    public function getText($element): string;
    /**
     * @param string $value
     */
    public function setText($element, $value): void;
}
