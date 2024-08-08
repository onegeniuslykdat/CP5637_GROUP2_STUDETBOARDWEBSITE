<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use BadMethodCallException;
use DOMDocument;
abstract class AbstractDomParser implements DomParserInterface
{
    protected static $domHtmlWrapperHelper = '____simple_html_dom__voku__html_wrapper____';
    protected static $domHtmlBrokenHtmlHelper = '____simple_html_dom__voku__broken_html____';
    protected static $domHtmlSpecialScriptHelper = '____simple_html_dom__voku__html_special_script____';
    protected static $domBrokenReplaceHelper = [];
    protected static $domLinkReplaceHelper = ['orig' => ['[', ']', '{', '}'], 'tmp' => ['____SIMPLE_HTML_DOM__VOKU__SQUARE_BRACKET_LEFT____', '____SIMPLE_HTML_DOM__VOKU__SQUARE_BRACKET_RIGHT____', '____SIMPLE_HTML_DOM__VOKU__BRACKET_LEFT____', '____SIMPLE_HTML_DOM__VOKU__BRACKET_RIGHT____']];
    protected static $domReplaceHelper = ['orig' => ['&', '|', '+', '%', '@', '<html ⚡'], 'tmp' => ['____SIMPLE_HTML_DOM__VOKU__AMP____', '____SIMPLE_HTML_DOM__VOKU__PIPE____', '____SIMPLE_HTML_DOM__VOKU__PLUS____', '____SIMPLE_HTML_DOM__VOKU__PERCENT____', '____SIMPLE_HTML_DOM__VOKU__AT____', '<html ____SIMPLE_HTML_DOM__VOKU__GOOGLE_AMP____="true"']];
    protected static $callback;
    protected static $functionAliases = [];
    protected $document;
    protected $encoding = 'UTF-8';
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);
        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist: ' . $name);
    }
    abstract public static function __callStatic($name, $arguments);
    public function __clone()
    {
        $this->document = clone $this->document;
    }
    abstract public function __get($name);
    abstract public function __toString();
    public function clear(): bool
    {
        return \true;
    }
    /**
     * @param string $html
     */
    abstract protected function createDOMDocument($html, $libXMLExtraOptions = null): DOMDocument;
    /**
     * @param string $content
     * @param bool $multiDecodeNewHtmlEntity
     */
    protected function decodeHtmlEntity($content, $multiDecodeNewHtmlEntity): string
    {
        if ($multiDecodeNewHtmlEntity) {
            if (\class_exists('Staatic\Vendor\voku\helper\UTF8')) {
                $content = UTF8::rawurldecode($content, \true);
            } else {
                do {
                    $content_compare = $content;
                    $content = \rawurldecode(\html_entity_decode($content, \ENT_QUOTES | \ENT_HTML5));
                } while ($content_compare !== $content);
            }
        } else if (\class_exists('Staatic\Vendor\voku\helper\UTF8')) {
            $content = UTF8::rawurldecode($content, \false);
        } else {
            $content = \rawurldecode(\html_entity_decode($content, \ENT_QUOTES | \ENT_HTML5));
        }
        return $content;
    }
    /**
     * @param string $selector
     */
    abstract public function find($selector, $idx = null);
    /**
     * @param string $selector
     */
    abstract public function findMulti($selector);
    /**
     * @param string $selector
     */
    abstract public function findMultiOrFalse($selector);
    /**
     * @param string $selector
     */
    abstract public function findOne($selector);
    /**
     * @param string $selector
     */
    abstract public function findOneOrFalse($selector);
    public function getDocument(): DOMDocument
    {
        return $this->document;
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    abstract public function html($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        $text = '';
        if ($this->document->documentElement) {
            foreach ($this->document->documentElement->childNodes as $node) {
                $text .= $this->document->saveHTML($node);
            }
        }
        return $this->fixHtmlOutput($text, $multiDecodeNewHtmlEntity, $putBrokenReplacedBack);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string
    {
        $text = '';
        if ($this->document->documentElement) {
            foreach ($this->document->documentElement->childNodes as $node) {
                $text .= $this->document->saveXML($node);
            }
        }
        return $this->fixHtmlOutput($text, $multiDecodeNewHtmlEntity);
    }
    /**
     * @param string $html
     */
    abstract public function loadHtml($html, $libXMLExtraOptions = null): DomParserInterface;
    /**
     * @param string $filePath
     */
    abstract public function loadHtmlFile($filePath, $libXMLExtraOptions = null): DomParserInterface;
    /**
     * @param string $filepath
     */
    public function save($filepath = ''): string
    {
        $string = $this->html();
        if ($filepath !== '') {
            \file_put_contents($filepath, $string, \LOCK_EX);
        }
        return $string;
    }
    public function set_callback($functionName)
    {
        static::$callback = $functionName;
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function text($multiDecodeNewHtmlEntity = \false): string
    {
        return $this->fixHtmlOutput($this->document->textContent, $multiDecodeNewHtmlEntity);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $htmlToXml
     * @param bool $removeXmlHeader
     * @param int $options
     */
    public function xml($multiDecodeNewHtmlEntity = \false, $htmlToXml = \true, $removeXmlHeader = \true, $options = \LIBXML_NOEMPTYTAG): string
    {
        $xml = $this->document->saveXML(null, $options);
        if ($xml === \false) {
            return '';
        }
        if ($removeXmlHeader) {
            $xml = \ltrim((string) \preg_replace('/<\?xml.*\?>/', '', $xml));
        }
        if ($htmlToXml) {
            $return = $this->fixHtmlOutput($xml, $multiDecodeNewHtmlEntity);
        } else {
            $xml = $this->decodeHtmlEntity($xml, $multiDecodeNewHtmlEntity);
            $return = self::putReplacedBackToPreserveHtmlEntities($xml);
        }
        return $return;
    }
    protected function getEncoding(): string
    {
        return $this->encoding;
    }
    /**
     * @param string $html
     */
    protected function html5FallbackForScriptTags(&$html)
    {
        $regExSpecialScript = '/<script(?<attr>[^>]*?)>(?<content>.*)<\/script>/isU';
        $htmlTmp = \preg_replace_callback($regExSpecialScript, static function ($scripts) {
            if (empty($scripts['content'])) {
                return $scripts[0];
            }
            return '<script' . $scripts['attr'] . '>' . \str_replace('</', '<\/', $scripts['content']) . '</script>';
        }, $html);
        if ($htmlTmp !== null) {
            $html = $htmlTmp;
        }
    }
    /**
     * @param string $html
     * @param bool $putBrokenReplacedBack
     */
    public static function putReplacedBackToPreserveHtmlEntities($html, $putBrokenReplacedBack = \true): string
    {
        static $DOM_REPLACE__HELPER_CACHE = null;
        if ($DOM_REPLACE__HELPER_CACHE === null) {
            $DOM_REPLACE__HELPER_CACHE['tmp'] = \array_merge(self::$domLinkReplaceHelper['tmp'], self::$domReplaceHelper['tmp']);
            $DOM_REPLACE__HELPER_CACHE['orig'] = \array_merge(self::$domLinkReplaceHelper['orig'], self::$domReplaceHelper['orig']);
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__start'] = '<' . self::$domHtmlWrapperHelper . '>';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__end'] = '</' . self::$domHtmlWrapperHelper . '>';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__start'] = '';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__end'] = '';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__start_broken'] = self::$domHtmlWrapperHelper . '>';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_wrapper__end_broken'] = '</' . self::$domHtmlWrapperHelper;
            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__start_broken'] = '';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_wrapper__end_broken'] = '';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__start'] = '<' . self::$domHtmlSpecialScriptHelper;
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__end'] = '</' . self::$domHtmlSpecialScriptHelper . '>';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__start'] = '<script';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__end'] = '</script>';
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__start_broken'] = self::$domHtmlSpecialScriptHelper;
            $DOM_REPLACE__HELPER_CACHE['tmp']['html_special_script__end_broken'] = '</' . self::$domHtmlSpecialScriptHelper;
            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__start_broken'] = 'script';
            $DOM_REPLACE__HELPER_CACHE['orig']['html_special_script__end_broken'] = '</script';
        }
        if ($putBrokenReplacedBack === \true && isset(self::$domBrokenReplaceHelper['tmp']) && \count(self::$domBrokenReplaceHelper['tmp']) > 0) {
            $html = \str_ireplace(self::$domBrokenReplaceHelper['tmp'], self::$domBrokenReplaceHelper['orig'], $html);
        }
        return \str_ireplace($DOM_REPLACE__HELPER_CACHE['tmp'], $DOM_REPLACE__HELPER_CACHE['orig'], $html);
    }
    /**
     * @param string $html
     */
    public static function replaceToPreserveHtmlEntities($html): string
    {
        $linksNew = [];
        $linksOld = [];
        if (\strpos($html, 'http') !== \false) {
            $regExUrl = '/(\[?\bhttps?:\/\/[^\s<>]+(?:\(\w+\)|[^[:punct:]\s]|\/|}|]))/i';
            \preg_match_all($regExUrl, $html, $linksOld);
            if (!empty($linksOld[1])) {
                $linksOld = $linksOld[1];
                foreach ((array) $linksOld as $linkKey => $linkOld) {
                    $linksNew[$linkKey] = \str_replace(self::$domLinkReplaceHelper['orig'], self::$domLinkReplaceHelper['tmp'], $linkOld);
                }
            }
        }
        $linksNewCount = \count($linksNew);
        if ($linksNewCount > 0 && \count($linksOld) === $linksNewCount) {
            $search = \array_merge($linksOld, self::$domReplaceHelper['orig']);
            $replace = \array_merge($linksNew, self::$domReplaceHelper['tmp']);
        } else {
            $search = self::$domReplaceHelper['orig'];
            $replace = self::$domReplaceHelper['tmp'];
        }
        return \str_replace($search, $replace, $html);
    }
}
