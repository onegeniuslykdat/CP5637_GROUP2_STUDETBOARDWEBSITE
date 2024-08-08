<?php

namespace Staatic\Crawler\DomParser;

use Staatic\Vendor\Masterminds\HTML5;
final class Html5DomParser implements DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html)
    {
        $html5 = new HTML5(['disable_html_ns' => \true]);
        return $html5->loadHTML($html);
    }
    public function getHtml($document): string
    {
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
