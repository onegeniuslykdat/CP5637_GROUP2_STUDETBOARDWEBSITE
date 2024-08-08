<?php

namespace Staatic\Vendor\voku\helper;

use DOMDocument;
interface DomParserInterface
{
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null);
    /**
     * @param string $selector
     */
    public function findMulti($selector);
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector);
    /**
     * @param string $selector
     */
    public function findOne($selector);
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector);
    /**
     * @param string $content
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function fixHtmlOutput($content, $multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    public function getDocument(): DOMDocument;
    /**
     * @param string $class
     */
    public function getElementByClass($class);
    /**
     * @param string $id
     */
    public function getElementById($id);
    /**
     * @param string $name
     */
    public function getElementByTagName($name);
    /**
     * @param string $id
     */
    public function getElementsById($id, $idx = null);
    /**
     * @param string $name
     */
    public function getElementsByTagName($name, $idx = null);
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function html($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string;
    /**
     * @param string $html
     */
    public function loadHtml($html, $libXMLExtraOptions = null): self;
    /**
     * @param string $filePath
     */
    public function loadHtmlFile($filePath, $libXMLExtraOptions = null): self;
    /**
     * @param string $filepath
     */
    public function save($filepath = ''): string;
    public function set_callback($functionName);
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function text($multiDecodeNewHtmlEntity = \false): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $htmlToXml
     * @param bool $removeXmlHeader
     * @param int $options
     */
    public function xml($multiDecodeNewHtmlEntity = \false, $htmlToXml = \true, $removeXmlHeader = \true, $options = \LIBXML_NOEMPTYTAG): string;
}
