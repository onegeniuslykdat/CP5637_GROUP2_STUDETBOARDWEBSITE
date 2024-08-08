<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use DOMImplementation;
use DOMElement;
use DOMDocument;
use DOMException;
use Exception;
use Staatic\Vendor\Masterminds\HTML5\Elements;
use Staatic\Vendor\Masterminds\HTML5\InstructionProcessor;
class DOMTreeBuilder implements EventHandler
{
    const NAMESPACE_HTML = 'http://www.w3.org/1999/xhtml';
    const NAMESPACE_MATHML = 'http://www.w3.org/1998/Math/MathML';
    const NAMESPACE_SVG = 'http://www.w3.org/2000/svg';
    const NAMESPACE_XLINK = 'http://www.w3.org/1999/xlink';
    const NAMESPACE_XML = 'http://www.w3.org/XML/1998/namespace';
    const NAMESPACE_XMLNS = 'http://www.w3.org/2000/xmlns/';
    const OPT_DISABLE_HTML_NS = 'disable_html_ns';
    const OPT_TARGET_DOC = 'target_document';
    const OPT_IMPLICIT_NS = 'implicit_namespaces';
    protected $nsRoots = array('html' => self::NAMESPACE_HTML, 'svg' => self::NAMESPACE_SVG, 'math' => self::NAMESPACE_MATHML);
    protected $implicitNamespaces = array('xml' => self::NAMESPACE_XML, 'xmlns' => self::NAMESPACE_XMLNS, 'xlink' => self::NAMESPACE_XLINK);
    protected $nsStack = array();
    protected $pushes = array();
    const IM_INITIAL = 0;
    const IM_BEFORE_HTML = 1;
    const IM_BEFORE_HEAD = 2;
    const IM_IN_HEAD = 3;
    const IM_IN_HEAD_NOSCRIPT = 4;
    const IM_AFTER_HEAD = 5;
    const IM_IN_BODY = 6;
    const IM_TEXT = 7;
    const IM_IN_TABLE = 8;
    const IM_IN_TABLE_TEXT = 9;
    const IM_IN_CAPTION = 10;
    const IM_IN_COLUMN_GROUP = 11;
    const IM_IN_TABLE_BODY = 12;
    const IM_IN_ROW = 13;
    const IM_IN_CELL = 14;
    const IM_IN_SELECT = 15;
    const IM_IN_SELECT_IN_TABLE = 16;
    const IM_AFTER_BODY = 17;
    const IM_IN_FRAMESET = 18;
    const IM_AFTER_FRAMESET = 19;
    const IM_AFTER_AFTER_BODY = 20;
    const IM_AFTER_AFTER_FRAMESET = 21;
    const IM_IN_SVG = 22;
    const IM_IN_MATHML = 23;
    protected $options = array();
    protected $stack = array();
    protected $current;
    protected $rules;
    protected $doc;
    protected $frag;
    protected $processor;
    protected $insertMode = 0;
    protected $onlyInline;
    protected $quirks = \true;
    protected $errors = array();
    public function __construct($isFragment = \false, array $options = array())
    {
        $this->options = $options;
        if (isset($options[self::OPT_TARGET_DOC])) {
            $this->doc = $options[self::OPT_TARGET_DOC];
        } else {
            $impl = new DOMImplementation();
            $dt = $impl->createDocumentType('html');
            $this->doc = $impl->createDocument(null, '', $dt);
            $this->doc->encoding = (!empty($options['encoding'])) ? $options['encoding'] : 'UTF-8';
        }
        $this->errors = array();
        $this->current = $this->doc;
        $this->rules = new TreeBuildingRules();
        $implicitNS = array();
        if (isset($this->options[self::OPT_IMPLICIT_NS])) {
            $implicitNS = $this->options[self::OPT_IMPLICIT_NS];
        } elseif (isset($this->options['implicitNamespaces'])) {
            $implicitNS = $this->options['implicitNamespaces'];
        }
        array_unshift($this->nsStack, $implicitNS + array('' => self::NAMESPACE_HTML) + $this->implicitNamespaces);
        if ($isFragment) {
            $this->insertMode = static::IM_IN_BODY;
            $this->frag = $this->doc->createDocumentFragment();
            $this->current = $this->frag;
        }
    }
    public function document()
    {
        return $this->doc;
    }
    public function fragment()
    {
        return $this->frag;
    }
    /**
     * @param InstructionProcessor $proc
     */
    public function setInstructionProcessor($proc)
    {
        $this->processor = $proc;
    }
    public function doctype($name, $idType = 0, $id = null, $quirks = \false)
    {
        $this->quirks = $quirks;
        if ($this->insertMode > static::IM_INITIAL) {
            $this->parseError('Illegal placement of DOCTYPE tag. Ignoring: ' . $name);
            return;
        }
        $this->insertMode = static::IM_BEFORE_HTML;
    }
    public function startTag($name, $attributes = array(), $selfClosing = \false)
    {
        $lname = $this->normalizeTagName($name);
        if (!$this->doc->documentElement && 'html' !== $name && !$this->frag) {
            $this->startTag('html');
        }
        if ($this->insertMode === static::IM_INITIAL) {
            $this->quirks = \true;
            $this->parseError('No DOCTYPE specified.');
        }
        if ('image' === $name && !($this->insertMode === static::IM_IN_SVG || $this->insertMode === static::IM_IN_MATHML)) {
            $name = 'img';
        }
        if ($this->insertMode >= static::IM_IN_BODY && Elements::isA($name, Elements::AUTOCLOSE_P)) {
            $this->autoclose('p');
        }
        switch ($name) {
            case 'html':
                $this->insertMode = static::IM_BEFORE_HEAD;
                break;
            case 'head':
                if ($this->insertMode > static::IM_BEFORE_HEAD) {
                    $this->parseError('Unexpected head tag outside of head context.');
                } else {
                    $this->insertMode = static::IM_IN_HEAD;
                }
                break;
            case 'body':
                $this->insertMode = static::IM_IN_BODY;
                break;
            case 'svg':
                $this->insertMode = static::IM_IN_SVG;
                break;
            case 'math':
                $this->insertMode = static::IM_IN_MATHML;
                break;
            case 'noscript':
                if ($this->insertMode === static::IM_IN_HEAD) {
                    $this->insertMode = static::IM_IN_HEAD_NOSCRIPT;
                }
                break;
        }
        if ($this->insertMode === static::IM_IN_SVG) {
            $lname = Elements::normalizeSvgElement($lname);
        }
        $pushes = 0;
        if (isset($this->nsRoots[$lname]) && $this->nsStack[0][''] !== $this->nsRoots[$lname]) {
            array_unshift($this->nsStack, array('' => $this->nsRoots[$lname]) + $this->nsStack[0]);
            ++$pushes;
        }
        $needsWorkaround = \false;
        if (isset($this->options['xmlNamespaces']) && $this->options['xmlNamespaces']) {
            foreach ($attributes as $aName => $aVal) {
                if ('xmlns' === $aName) {
                    $needsWorkaround = $aVal;
                    array_unshift($this->nsStack, array('' => $aVal) + $this->nsStack[0]);
                    ++$pushes;
                } elseif ('xmlns' === (($pos = strpos($aName, ':')) ? substr($aName, 0, $pos) : '')) {
                    array_unshift($this->nsStack, array(substr($aName, $pos + 1) => $aVal) + $this->nsStack[0]);
                    ++$pushes;
                }
            }
        }
        if ($this->onlyInline && Elements::isA($lname, Elements::BLOCK_TAG)) {
            $this->autoclose($this->onlyInline);
            $this->onlyInline = null;
        }
        if ($this->current instanceof DOMElement && isset(Elements::$optionalEndElementsParentsToClose[$lname])) {
            foreach (Elements::$optionalEndElementsParentsToClose[$lname] as $parentElName) {
                if ($this->current instanceof DOMElement && $this->current->tagName === $parentElName) {
                    $this->autoclose($parentElName);
                }
            }
        }
        try {
            $prefix = ($pos = strpos($lname, ':')) ? substr($lname, 0, $pos) : '';
            if (\false !== $needsWorkaround) {
                $xml = "<{$lname} xmlns=\"{$needsWorkaround}\" " . ((strlen($prefix) && isset($this->nsStack[0][$prefix])) ? "xmlns:{$prefix}=\"" . $this->nsStack[0][$prefix] . '"' : '') . '/>';
                $frag = new DOMDocument('1.0', 'UTF-8');
                $frag->loadXML($xml);
                $ele = $this->doc->importNode($frag->documentElement, \true);
            } else if (!isset($this->nsStack[0][$prefix]) || '' === $prefix && isset($this->options[self::OPT_DISABLE_HTML_NS]) && $this->options[self::OPT_DISABLE_HTML_NS]) {
                $ele = $this->doc->createElement($lname);
            } else {
                $ele = $this->doc->createElementNS($this->nsStack[0][$prefix], $lname);
            }
        } catch (DOMException $e) {
            $this->parseError("Illegal tag name: <{$lname}>. Replaced with <invalid>.");
            $ele = $this->doc->createElement('invalid');
        }
        if (Elements::isA($lname, Elements::BLOCK_ONLY_INLINE)) {
            $this->onlyInline = $lname;
        }
        if ($pushes > 0 && !Elements::isA($name, Elements::VOID_TAG)) {
            $this->pushes[spl_object_hash($ele)] = array($pushes, $ele);
        }
        foreach ($attributes as $aName => $aVal) {
            if ('xmlns' === $aName) {
                continue;
            }
            if ($this->insertMode === static::IM_IN_SVG) {
                $aName = Elements::normalizeSvgAttribute($aName);
            } elseif ($this->insertMode === static::IM_IN_MATHML) {
                $aName = Elements::normalizeMathMlAttribute($aName);
            }
            $aVal = (string) $aVal;
            try {
                $prefix = ($pos = strpos($aName, ':')) ? substr($aName, 0, $pos) : \false;
                if ('xmlns' === $prefix) {
                    $ele->setAttributeNS(self::NAMESPACE_XMLNS, $aName, $aVal);
                } elseif (\false !== $prefix && isset($this->nsStack[0][$prefix])) {
                    $ele->setAttributeNS($this->nsStack[0][$prefix], $aName, $aVal);
                } else {
                    $ele->setAttribute($aName, $aVal);
                }
            } catch (DOMException $e) {
                $this->parseError("Illegal attribute name for tag {$name}. Ignoring: {$aName}");
                continue;
            }
            if ('id' === $aName) {
                $ele->setIdAttribute('id', \true);
            }
        }
        if ($this->frag !== $this->current && $this->rules->hasRules($name)) {
            $this->current = $this->rules->evaluate($ele, $this->current);
        } else {
            $this->current->appendChild($ele);
            if (!Elements::isA($name, Elements::VOID_TAG)) {
                $this->current = $ele;
            }
            if (Elements::isHtml5Element($name)) {
                $selfClosing = \false;
            }
        }
        if ($this->insertMode <= static::IM_BEFORE_HEAD && 'head' !== $name && 'html' !== $name) {
            $this->insertMode = static::IM_IN_BODY;
        }
        if ($pushes > 0 && Elements::isA($name, Elements::VOID_TAG)) {
            for ($i = 0; $i < $pushes; ++$i) {
                array_shift($this->nsStack);
            }
        }
        if ($selfClosing) {
            $this->endTag($name);
        }
        return Elements::element($name);
    }
    public function endTag($name)
    {
        $lname = $this->normalizeTagName($name);
        if ('br' === $name) {
            $this->parseError('Closing tag encountered for void element br.');
            $this->startTag('br');
        } elseif (Elements::isA($name, Elements::VOID_TAG)) {
            return;
        }
        if ($this->insertMode <= static::IM_BEFORE_HTML) {
            if (in_array($name, array('html', 'br', 'head', 'title'))) {
                $this->startTag('html');
                $this->endTag($name);
                $this->insertMode = static::IM_BEFORE_HEAD;
                return;
            }
            $this->parseError('Illegal closing tag at global scope.');
            return;
        }
        if ($this->insertMode === static::IM_IN_SVG) {
            $lname = Elements::normalizeSvgElement($lname);
        }
        $cid = spl_object_hash($this->current);
        if ('html' === $lname) {
            return;
        }
        if (isset($this->pushes[$cid])) {
            for ($i = 0; $i < $this->pushes[$cid][0]; ++$i) {
                array_shift($this->nsStack);
            }
            unset($this->pushes[$cid]);
        }
        if (!$this->autoclose($lname)) {
            $this->parseError('Could not find closing tag for ' . $lname);
        }
        switch ($lname) {
            case 'head':
                $this->insertMode = static::IM_AFTER_HEAD;
                break;
            case 'body':
                $this->insertMode = static::IM_AFTER_BODY;
                break;
            case 'svg':
            case 'mathml':
                $this->insertMode = static::IM_IN_BODY;
                break;
        }
    }
    public function comment($cdata)
    {
        $node = $this->doc->createComment($cdata);
        $this->current->appendChild($node);
    }
    public function text($data)
    {
        if ($this->insertMode < static::IM_IN_HEAD) {
            $dataTmp = trim($data, " \t\n\r\f");
            if (!empty($dataTmp)) {
                $this->parseError('Unexpected text. Ignoring: ' . $dataTmp);
            }
            return;
        }
        $node = $this->doc->createTextNode($data);
        $this->current->appendChild($node);
    }
    public function eof()
    {
    }
    public function parseError($msg, $line = 0, $col = 0)
    {
        $this->errors[] = sprintf('Line %d, Col %d: %s', $line, $col, $msg);
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function cdata($data)
    {
        $node = $this->doc->createCDATASection($data);
        $this->current->appendChild($node);
    }
    public function processingInstruction($name, $data = null)
    {
        if ($this->insertMode === static::IM_INITIAL && 'xml' === strtolower($name)) {
            return;
        }
        if ($this->processor instanceof InstructionProcessor) {
            $res = $this->processor->process($this->current, $name, $data);
            if (!empty($res)) {
                $this->current = $res;
            }
            return;
        }
        $node = $this->doc->createProcessingInstruction($name, $data);
        $this->current->appendChild($node);
    }
    protected function normalizeTagName($tagName)
    {
        return $tagName;
    }
    protected function quirksTreeResolver($name)
    {
        throw new Exception('Not implemented.');
    }
    protected function autoclose($tagName)
    {
        $working = $this->current;
        do {
            if (\XML_ELEMENT_NODE !== $working->nodeType) {
                return \false;
            }
            if ($working->tagName === $tagName) {
                $this->current = $working->parentNode;
                return \true;
            }
        } while ($working = $working->parentNode);
        return \false;
    }
    protected function isAncestor($tagName)
    {
        $candidate = $this->current;
        while (\XML_ELEMENT_NODE === $candidate->nodeType) {
            if ($candidate->tagName === $tagName) {
                return \true;
            }
            $candidate = $candidate->parentNode;
        }
        return \false;
    }
    protected function isParent($tagName)
    {
        return $this->current->tagName === $tagName;
    }
}
