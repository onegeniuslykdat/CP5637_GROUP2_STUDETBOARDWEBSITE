<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use IteratorAggregate;
use BadMethodCallException;
use DOMNode;
class SimpleHtmlDomBlank extends AbstractSimpleHtmlDom implements IteratorAggregate, SimpleHtmlDomInterface
{
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);
        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist');
    }
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
    }
    public function getTag(): string
    {
        return '';
    }
    public function getAllAttributes()
    {
        return null;
    }
    public function hasAttributes(): bool
    {
        return \false;
    }
    /**
     * @param string $name
     */
    public function getAttribute($name): string
    {
        return '';
    }
    /**
     * @param string $name
     */
    public function hasAttribute($name): bool
    {
        return \false;
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function html($multiDecodeNewHtmlEntity = \false): string
    {
        return '';
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        return '';
    }
    /**
     * @param string $name
     */
    public function removeAttribute($name): SimpleHtmlDomInterface
    {
        return $this;
    }
    public function removeAttributes(): SimpleHtmlDomInterface
    {
        return $this;
    }
    /**
     * @param string $string
     * @param bool $putBrokenReplacedBack
     */
    protected function replaceChildWithString($string, $putBrokenReplacedBack = \true): SimpleHtmlDomInterface
    {
        return new static();
    }
    /**
     * @param string $string
     */
    protected function replaceNodeWithString($string): SimpleHtmlDomInterface
    {
        return new static();
    }
    protected function replaceTextWithString($string): SimpleHtmlDomInterface
    {
        return new static();
    }
    /**
     * @param string $name
     * @param bool $strictEmptyValueCheck
     */
    public function setAttribute($name, $value = null, $strictEmptyValueCheck = \false): SimpleHtmlDomInterface
    {
        return $this;
    }
    public function text(): string
    {
        return '';
    }
    /**
     * @param int $idx
     */
    public function childNodes($idx = -1)
    {
        return null;
    }
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
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
    public function findOne($selector): SimpleHtmlDomInterface
    {
        return new static();
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        return \false;
    }
    public function firstChild()
    {
        return null;
    }
    /**
     * @param string $class
     */
    public function getElementByClass($class): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
    }
    /**
     * @param string $id
     */
    public function getElementById($id): SimpleHtmlDomInterface
    {
        return new static();
    }
    /**
     * @param string $name
     */
    public function getElementByTagName($name): SimpleHtmlDomInterface
    {
        return new static();
    }
    /**
     * @param string $id
     */
    public function getElementsById($id, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
    }
    /**
     * @param string $name
     */
    public function getElementsByTagName($name, $idx = null)
    {
        return new SimpleHtmlDomNodeBlank();
    }
    public function getHtmlDomParser(): HtmlDomParser
    {
        return new HtmlDomParser($this);
    }
    public function getNode(): DOMNode
    {
        return new DOMNode();
    }
    public function isRemoved(): bool
    {
        return \true;
    }
    public function lastChild()
    {
        return null;
    }
    public function nextSibling()
    {
        return null;
    }
    public function nextNonWhitespaceSibling()
    {
        return null;
    }
    public function previousNonWhitespaceSibling()
    {
        return null;
    }
    public function parentNode(): ?SimpleHtmlDomInterface
    {
        return new static();
    }
    public function previousSibling()
    {
        return null;
    }
    public function val($value = null)
    {
        return null;
    }
    public function getIterator(): SimpleHtmlDomNodeInterface
    {
        return new SimpleHtmlDomNodeBlank();
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string
    {
        return '';
    }
    public function delete()
    {
        $this->outertext = '';
    }
}
