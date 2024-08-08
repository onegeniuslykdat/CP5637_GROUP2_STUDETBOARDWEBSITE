<?php

namespace Staatic\Vendor\Masterminds\HTML5\Serializer;

use DOMCharacterData;
use DOMElement;
use DOMXPath;
use DOMNode;
use DOMAttr;
use Staatic\Vendor\Masterminds\HTML5\Elements;
class OutputRules implements RulesInterface
{
    const NAMESPACE_HTML = 'http://www.w3.org/1999/xhtml';
    const NAMESPACE_MATHML = 'http://www.w3.org/1998/Math/MathML';
    const NAMESPACE_SVG = 'http://www.w3.org/2000/svg';
    const NAMESPACE_XLINK = 'http://www.w3.org/1999/xlink';
    const NAMESPACE_XML = 'http://www.w3.org/XML/1998/namespace';
    const NAMESPACE_XMLNS = 'http://www.w3.org/2000/xmlns/';
    protected $implicitNamespaces = array(self::NAMESPACE_HTML, self::NAMESPACE_SVG, self::NAMESPACE_MATHML, self::NAMESPACE_XML, self::NAMESPACE_XMLNS);
    const IM_IN_HTML = 1;
    const IM_IN_SVG = 2;
    const IM_IN_MATHML = 3;
    private $hasHTML5 = \false;
    protected $traverser;
    protected $encode = \false;
    protected $out;
    protected $outputMode;
    private $xpath;
    protected $nonBooleanAttributes = array(array('nodeNamespace' => 'http://www.w3.org/1999/xhtml', 'attrName' => array('href', 'hreflang', 'http-equiv', 'icon', 'id', 'keytype', 'kind', 'label', 'lang', 'language', 'list', 'maxlength', 'media', 'method', 'name', 'placeholder', 'rel', 'rows', 'rowspan', 'sandbox', 'spellcheck', 'scope', 'seamless', 'shape', 'size', 'sizes', 'span', 'src', 'srcdoc', 'srclang', 'srcset', 'start', 'step', 'style', 'summary', 'tabindex', 'target', 'title', 'type', 'value', 'width', 'border', 'charset', 'cite', 'class', 'code', 'codebase', 'color', 'cols', 'colspan', 'content', 'coords', 'data', 'datetime', 'default', 'dir', 'dirname', 'enctype', 'for', 'form', 'formaction', 'headers', 'height', 'accept', 'accept-charset', 'accesskey', 'action', 'align', 'alt', 'bgcolor')), array('nodeNamespace' => 'http://www.w3.org/1999/xhtml', 'xpath' => 'starts-with(local-name(), \'data-\')'));
    const DOCTYPE = '<!DOCTYPE html>';
    public function __construct($output, $options = array())
    {
        if (isset($options['encode_entities'])) {
            $this->encode = $options['encode_entities'];
        }
        $this->outputMode = static::IM_IN_HTML;
        $this->out = $output;
        $this->hasHTML5 = defined('ENT_HTML5');
    }
    /**
     * @param mixed[] $rule
     */
    public function addRule($rule)
    {
        $this->nonBooleanAttributes[] = $rule;
    }
    /**
     * @param Traverser $traverser
     */
    public function setTraverser($traverser)
    {
        $this->traverser = $traverser;
        return $this;
    }
    public function unsetTraverser()
    {
        $this->traverser = null;
        return $this;
    }
    public function document($dom)
    {
        $this->doctype();
        if ($dom->documentElement) {
            foreach ($dom->childNodes as $node) {
                $this->traverser->node($node);
            }
            $this->nl();
        }
    }
    protected function doctype()
    {
        $this->wr(static::DOCTYPE);
        $this->nl();
    }
    public function element($ele)
    {
        $name = $ele->tagName;
        if ($this->traverser->isLocalElement($ele)) {
            $name = $ele->localName;
        }
        if ('svg' == $name) {
            $this->outputMode = static::IM_IN_SVG;
            $name = Elements::normalizeSvgElement($name);
        } elseif ('math' == $name) {
            $this->outputMode = static::IM_IN_MATHML;
        }
        $this->openTag($ele);
        if (Elements::isA($name, Elements::TEXT_RAW)) {
            foreach ($ele->childNodes as $child) {
                if ($child instanceof DOMCharacterData) {
                    $this->wr($child->data);
                } elseif ($child instanceof DOMElement) {
                    $this->element($child);
                }
            }
        } else {
            if ($ele->hasChildNodes()) {
                $this->traverser->children($ele->childNodes);
            }
            if ('svg' == $name || 'math' == $name) {
                $this->outputMode = static::IM_IN_HTML;
            }
        }
        if (!Elements::isA($name, Elements::VOID_TAG)) {
            $this->closeTag($ele);
        }
    }
    public function text($ele)
    {
        if (isset($ele->parentNode) && isset($ele->parentNode->tagName) && Elements::isA($ele->parentNode->localName, Elements::TEXT_RAW)) {
            $this->wr($ele->data);
            return;
        }
        $this->wr($this->enc($ele->data));
    }
    public function cdata($ele)
    {
        $this->wr($ele->ownerDocument->saveXML($ele));
    }
    public function comment($ele)
    {
        $this->wr($ele->ownerDocument->saveXML($ele));
    }
    public function processorInstruction($ele)
    {
        $this->wr('<?')->wr($ele->target)->wr(' ')->wr($ele->data)->wr('?>');
    }
    protected function namespaceAttrs($ele)
    {
        if (!$this->xpath || $this->xpath->document !== $ele->ownerDocument) {
            $this->xpath = new DOMXPath($ele->ownerDocument);
        }
        foreach ($this->xpath->query('namespace::*[not(.=../../namespace::*)]', $ele) as $nsNode) {
            if (!in_array($nsNode->nodeValue, $this->implicitNamespaces)) {
                $this->wr(' ')->wr($nsNode->nodeName)->wr('="')->wr($nsNode->nodeValue)->wr('"');
            }
        }
    }
    protected function openTag($ele)
    {
        $this->wr('<')->wr($this->traverser->isLocalElement($ele) ? $ele->localName : $ele->tagName);
        $this->attrs($ele);
        $this->namespaceAttrs($ele);
        if ($this->outputMode == static::IM_IN_HTML) {
            $this->wr('>');
        } else if ($ele->hasChildNodes()) {
            $this->wr('>');
        } else {
            $this->wr(' />');
        }
    }
    protected function attrs($ele)
    {
        if (!$ele->hasAttributes()) {
            return $this;
        }
        $map = $ele->attributes;
        $len = $map->length;
        for ($i = 0; $i < $len; ++$i) {
            $node = $map->item($i);
            $val = $this->enc($node->value, \true);
            $name = $node->nodeName;
            if ($this->outputMode == static::IM_IN_SVG) {
                $name = Elements::normalizeSvgAttribute($name);
            } elseif ($this->outputMode == static::IM_IN_MATHML) {
                $name = Elements::normalizeMathMlAttribute($name);
            }
            $this->wr(' ')->wr($name);
            if (isset($val) && '' !== $val || $this->nonBooleanAttribute($node)) {
                $this->wr('="')->wr($val)->wr('"');
            }
        }
    }
    /**
     * @param DOMAttr $attr
     */
    protected function nonBooleanAttribute($attr)
    {
        $ele = $attr->ownerElement;
        foreach ($this->nonBooleanAttributes as $rule) {
            if (isset($rule['nodeNamespace']) && $rule['nodeNamespace'] !== $ele->namespaceURI) {
                continue;
            }
            if (isset($rule['attNamespace']) && $rule['attNamespace'] !== $attr->namespaceURI) {
                continue;
            }
            if (isset($rule['nodeName']) && !is_array($rule['nodeName']) && $rule['nodeName'] !== $ele->localName) {
                continue;
            }
            if (isset($rule['nodeName']) && is_array($rule['nodeName']) && !in_array($ele->localName, $rule['nodeName'], \true)) {
                continue;
            }
            if (isset($rule['attrName']) && !is_array($rule['attrName']) && $rule['attrName'] !== $attr->localName) {
                continue;
            }
            if (isset($rule['attrName']) && is_array($rule['attrName']) && !in_array($attr->localName, $rule['attrName'], \true)) {
                continue;
            }
            if (isset($rule['xpath'])) {
                $xp = $this->getXPath($attr);
                if (isset($rule['prefixes'])) {
                    foreach ($rule['prefixes'] as $nsPrefix => $ns) {
                        $xp->registerNamespace($nsPrefix, $ns);
                    }
                }
                if (!$xp->evaluate($rule['xpath'], $attr)) {
                    continue;
                }
            }
            return \true;
        }
        return \false;
    }
    private function getXPath(DOMNode $node)
    {
        if (!$this->xpath) {
            $this->xpath = new DOMXPath($node->ownerDocument);
        }
        return $this->xpath;
    }
    protected function closeTag($ele)
    {
        if ($this->outputMode == static::IM_IN_HTML || $ele->hasChildNodes()) {
            $this->wr('</')->wr($this->traverser->isLocalElement($ele) ? $ele->localName : $ele->tagName)->wr('>');
        }
    }
    protected function wr($text)
    {
        fwrite($this->out, $text);
        return $this;
    }
    protected function nl()
    {
        fwrite($this->out, \PHP_EOL);
        return $this;
    }
    protected function enc($text, $attribute = \false)
    {
        if (!$this->encode) {
            return $this->escape($text, $attribute);
        }
        if ($this->hasHTML5) {
            return htmlentities($text, \ENT_HTML5 | \ENT_SUBSTITUTE | \ENT_QUOTES, 'UTF-8', \false);
        } else {
            return strtr($text, HTML5Entities::$map);
        }
    }
    protected function escape($text, $attribute = \false)
    {
        if ($attribute) {
            $replace = array('"' => '&quot;', '&' => '&amp;', " " => '&nbsp;');
        } else {
            $replace = array('<' => '&lt;', '>' => '&gt;', '&' => '&amp;', " " => '&nbsp;');
        }
        return strtr($text, $replace);
    }
}
