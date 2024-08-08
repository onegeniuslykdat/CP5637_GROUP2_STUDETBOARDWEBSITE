<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

class SimpleHtmlDomNode extends AbstractSimpleHtmlDomNode implements SimpleHtmlDomNodeInterface
{
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null)
    {
        $elements = new static();
        foreach ($this as $node) {
            \assert($node instanceof SimpleHtmlDomInterface);
            foreach ($node->find($selector) as $res) {
                $elements[] = $res;
            }
        }
        if ($idx === null) {
            if (\count($elements) === 0) {
                return new SimpleHtmlDomNodeBlank();
            }
            return $elements;
        }
        if ($idx < 0) {
            $idx = \count($elements) + $idx;
        }
        return $elements[$idx] ?? null;
    }
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleHtmlDomNodeInterface
    {
        return $this->find($selector, null);
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        $return = $this->find($selector, null);
        if ($return instanceof SimpleHtmlDomNodeBlank) {
            return \false;
        }
        return $return;
    }
    /**
     * @param string $selector
     */
    public function findOne($selector)
    {
        $return = $this->find($selector, 0);
        return $return ?? new SimpleHtmlDomNodeBlank();
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        $return = $this->find($selector, 0);
        return $return ?? \false;
    }
    public function innerHtml(): array
    {
        $html = [];
        foreach ($this as $node) {
            $html[] = $node->outertext;
        }
        return $html;
    }
    public function innertext()
    {
        return $this->innerHtml();
    }
    public function outertext()
    {
        return $this->innerHtml();
    }
    public function text(): array
    {
        $text = [];
        foreach ($this as $node) {
            $text[] = $node->plaintext;
        }
        return $text;
    }
}
