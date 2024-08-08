<?php

namespace Staatic\Vendor\voku\helper;

use IteratorAggregate;
use DOMNode;
interface SimpleXmlDomInterface extends IteratorAggregate
{
    public function __call($name, $arguments);
    public function __get($name);
    public function __invoke($selector, $idx = null);
    public function __isset($name);
    public function __toString();
    /**
     * @param int $idx
     */
    public function childNodes($idx = -1);
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null);
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleXmlDomNodeInterface;
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector);
    /**
     * @param string $selector
     */
    public function findOne($selector): self;
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector);
    public function firstChild();
    public function getAllAttributes();
    public function hasAttributes(): bool;
    /**
     * @param string $name
     */
    public function getAttribute($name): string;
    /**
     * @param string $class
     */
    public function getElementByClass($class);
    /**
     * @param string $id
     */
    public function getElementById($id): self;
    /**
     * @param string $name
     */
    public function getElementByTagName($name): self;
    /**
     * @param string $id
     */
    public function getElementsById($id, $idx = null);
    /**
     * @param string $name
     */
    public function getElementsByTagName($name, $idx = null);
    public function getIterator(): SimpleXmlDomNodeInterface;
    public function getNode(): DOMNode;
    public function getXmlDomParser(): XmlDomParser;
    /**
     * @param string $name
     */
    public function hasAttribute($name): bool;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string;
    public function isRemoved(): bool;
    public function lastChild();
    public function nextSibling();
    public function nextNonWhitespaceSibling();
    public function parentNode(): self;
    public function previousSibling();
    public function previousNonWhitespaceSibling();
    /**
     * @param string $name
     */
    public function removeAttribute($name): self;
    /**
     * @param string $name
     * @param bool $strictEmptyValueCheck
     */
    public function setAttribute($name, $value = null, $strictEmptyValueCheck = \false): self;
    public function text(): string;
    public function val($value = null);
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function xml($multiDecodeNewHtmlEntity = \false): string;
}
