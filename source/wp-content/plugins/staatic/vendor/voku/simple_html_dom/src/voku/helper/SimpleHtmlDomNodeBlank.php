<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

class SimpleHtmlDomNodeBlank extends AbstractSimpleHtmlDomNode implements SimpleHtmlDomNodeInterface
{
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null)
    {
        return null;
    }
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleHtmlDomNodeInterface
    {
        return new self();
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        return \false;
    }
    /**
     * @param string $selector
     */
    public function findOne($selector)
    {
        return new SimpleHtmlDomBlank();
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        return \false;
    }
    public function innerHtml(): array
    {
        return [];
    }
    public function innertext()
    {
        return [];
    }
    public function outertext()
    {
        return [];
    }
    public function text(): array
    {
        return [];
    }
}
