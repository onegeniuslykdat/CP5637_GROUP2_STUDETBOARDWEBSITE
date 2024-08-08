<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use BadMethodCallException;
abstract class AbstractSimpleHtmlDom
{
    protected static $functionAliases = ['children' => 'childNodes', 'first_child' => 'firstChild', 'last_child' => 'lastChild', 'next_sibling' => 'nextSibling', 'prev_sibling' => 'previousSibling', 'parent' => 'parentNode', 'outertext' => 'html', 'outerhtml' => 'html', 'innertext' => 'innerHtml', 'innerhtml' => 'innerHtml', 'innerhtmlkeep' => 'innerHtmlKeep'];
    protected static $stringDomNodes = ['id', 'prefix', 'content'];
    protected $node;
    private $classListCache;
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);
        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist');
    }
    public function __get($name)
    {
        $nameOrig = $name;
        $name = \strtolower($name);
        switch ($name) {
            case 'outerhtml':
            case 'outertext':
            case 'html':
                return $this->html();
            case 'innerhtml':
            case 'innertext':
                return $this->innerHtml();
            case 'innerhtmlkeep':
                return $this->innerHtml(\false, \false);
            case 'text':
            case 'plaintext':
                return $this->text();
            case 'tag':
                return $this->node->nodeName ?? '';
            case 'attr':
                return $this->getAllAttributes();
            case 'classlist':
                if ($this->classListCache === null) {
                    $this->classListCache = new SimpleHtmlAttributes($this->node ?? null, 'class');
                }
                return $this->classListCache;
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    if (\is_string($this->node->{$nameOrig})) {
                        return HtmlDomParser::putReplacedBackToPreserveHtmlEntities($this->node->{$nameOrig});
                    }
                    return $this->node->{$nameOrig};
                }
                return $this->getAttribute($name);
        }
    }
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }
    public function __isset($name)
    {
        $nameOrig = $name;
        $name = \strtolower($name);
        switch ($name) {
            case 'outertext':
            case 'outerhtml':
            case 'innertext':
            case 'innerhtml':
            case 'innerhtmlkeep':
            case 'plaintext':
            case 'text':
            case 'tag':
                return \true;
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    return isset($this->node->{$nameOrig});
                }
                return $this->hasAttribute($name);
        }
    }
    public function __set($name, $value)
    {
        $nameOrig = $name;
        $name = \strtolower($name);
        switch ($name) {
            case 'outerhtml':
            case 'outertext':
                return $this->replaceNodeWithString($value);
            case 'innertext':
            case 'innerhtml':
                return $this->replaceChildWithString($value);
            case 'innerhtmlkeep':
                return $this->replaceChildWithString($value, \false);
            case 'plaintext':
                return $this->replaceTextWithString($value);
            case 'classlist':
                $name = 'class';
                $nameOrig = 'class';
            default:
                if ($this->node && \property_exists($this->node, $nameOrig)) {
                    if (in_array($nameOrig, self::$stringDomNodes)) {
                        $value = (string) $value;
                    }
                    if (!is_null($value)) {
                        return $this->node->{$nameOrig} = $value;
                    }
                }
                return $this->setAttribute($name, $value);
        }
    }
    public function __toString()
    {
        return $this->html();
    }
    public function __unset($name)
    {
        $this->removeAttribute($name);
    }
    /**
     * @param string $selector
     */
    abstract public function find($selector, $idx = null);
    abstract public function getAllAttributes();
    /**
     * @param string $name
     */
    abstract public function getAttribute($name): string;
    /**
     * @param string $name
     */
    abstract public function hasAttribute($name): bool;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    abstract public function html($multiDecodeNewHtmlEntity = \false): string;
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    abstract public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string;
    /**
     * @param string $name
     */
    abstract public function removeAttribute($name): SimpleHtmlDomInterface;
    /**
     * @param string $string
     * @param bool $putBrokenReplacedBack
     */
    abstract protected function replaceChildWithString($string, $putBrokenReplacedBack = \true): SimpleHtmlDomInterface;
    /**
     * @param string $string
     */
    abstract protected function replaceNodeWithString($string): SimpleHtmlDomInterface;
    abstract protected function replaceTextWithString($string): SimpleHtmlDomInterface;
    /**
     * @param string $name
     * @param bool $strictEmptyValueCheck
     */
    abstract public function setAttribute($name, $value = null, $strictEmptyValueCheck = \false): SimpleHtmlDomInterface;
    abstract public function text(): string;
}
