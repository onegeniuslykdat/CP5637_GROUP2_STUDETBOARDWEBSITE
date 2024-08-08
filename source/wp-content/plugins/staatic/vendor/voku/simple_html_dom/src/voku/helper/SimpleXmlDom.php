<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use IteratorAggregate;
use DOMNode;
use BadMethodCallException;
use DOMElement;
use RuntimeException;
class SimpleXmlDom extends AbstractSimpleXmlDom implements IteratorAggregate, SimpleXmlDomInterface
{
    public function __construct(DOMNode $node)
    {
        $this->node = $node;
    }
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
        return $this->getXmlDomParser()->find($selector, $idx);
    }
    public function getAllAttributes()
    {
        if ($this->node && $this->node->hasAttributes()) {
            $attributes = [];
            foreach ($this->node->attributes ?? [] as $attr) {
                $attributes[$attr->name] = XmlDomParser::putReplacedBackToPreserveHtmlEntities($attr->value);
            }
            return $attributes;
        }
        return null;
    }
    public function hasAttributes(): bool
    {
        return $this->node->hasAttributes();
    }
    /**
     * @param string $name
     */
    public function getAttribute($name): string
    {
        if ($this->node instanceof DOMElement) {
            return XmlDomParser::putReplacedBackToPreserveHtmlEntities($this->node->getAttribute($name));
        }
        return '';
    }
    /**
     * @param string $name
     */
    public function hasAttribute($name): bool
    {
        if (!$this->node instanceof DOMElement) {
            return \false;
        }
        return $this->node->hasAttribute($name);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string
    {
        return $this->getXmlDomParser()->innerXml($multiDecodeNewHtmlEntity);
    }
    /**
     * @param string $name
     */
    public function removeAttribute($name): SimpleXmlDomInterface
    {
        if (\method_exists($this->node, 'removeAttribute')) {
            $this->node->removeAttribute($name);
        }
        return $this;
    }
    /**
     * @param string $string
     * @param bool $putBrokenReplacedBack
     */
    protected function replaceChildWithString($string, $putBrokenReplacedBack = \true): SimpleXmlDomInterface
    {
        if (!empty($string)) {
            $newDocument = new XmlDomParser($string);
            $tmpDomString = $this->normalizeStringForComparision($newDocument);
            $tmpStr = $this->normalizeStringForComparision($string);
            if ($tmpDomString !== $tmpStr) {
                throw new RuntimeException('Not valid XML fragment!' . "\n" . $tmpDomString . "\n" . $tmpStr);
            }
        }
        $remove_nodes = [];
        if ($this->node->childNodes->length > 0) {
            foreach ($this->node->childNodes as $node) {
                $remove_nodes[] = $node;
            }
        }
        foreach ($remove_nodes as $remove_node) {
            $this->node->removeChild($remove_node);
        }
        if (!empty($newDocument)) {
            $ownerDocument = $this->node->ownerDocument;
            if ($ownerDocument && $newDocument->getDocument()->documentElement) {
                $newNode = $ownerDocument->importNode($newDocument->getDocument()->documentElement, \true);
                $this->node->appendChild($newNode);
            }
        }
        return $this;
    }
    /**
     * @param string $string
     */
    protected function replaceNodeWithString($string): SimpleXmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            return $this;
        }
        $newDocument = new XmlDomParser($string);
        $tmpDomOuterTextString = $this->normalizeStringForComparision($newDocument);
        $tmpStr = $this->normalizeStringForComparision($string);
        if ($tmpDomOuterTextString !== $tmpStr) {
            throw new RuntimeException('Not valid XML fragment!' . "\n" . $tmpDomOuterTextString . "\n" . $tmpStr);
        }
        $ownerDocument = $this->node->ownerDocument;
        if ($ownerDocument === null || $newDocument->getDocument()->documentElement === null) {
            return $this;
        }
        $newNode = $ownerDocument->importNode($newDocument->getDocument()->documentElement, \true);
        $this->node->parentNode->replaceChild($newNode, $this->node);
        $this->node = $newNode;
        return $this;
    }
    protected function replaceTextWithString($string): SimpleXmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            return $this;
        }
        $ownerDocument = $this->node->ownerDocument;
        if ($ownerDocument) {
            $newElement = $ownerDocument->createTextNode($string);
            $newNode = $ownerDocument->importNode($newElement, \true);
            $this->node->parentNode->replaceChild($newNode, $this->node);
            $this->node = $newNode;
        }
        return $this;
    }
    /**
     * @param string $name
     * @param bool $strictEmptyValueCheck
     */
    public function setAttribute($name, $value = null, $strictEmptyValueCheck = \false): SimpleXmlDomInterface
    {
        if ($strictEmptyValueCheck && $value === null || !$strictEmptyValueCheck && empty($value)) {
            $this->removeAttribute($name);
        } elseif (\method_exists($this->node, 'setAttribute')) {
            $this->node->setAttribute($name, HtmlDomParser::replaceToPreserveHtmlEntities((string) $value));
        }
        return $this;
    }
    public function text(): string
    {
        return $this->getXmlDomParser()->fixHtmlOutput($this->node->textContent);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function xml($multiDecodeNewHtmlEntity = \false): string
    {
        return $this->getXmlDomParser()->xml($multiDecodeNewHtmlEntity, \false);
    }
    /**
     * @param DOMNode $node
     * @param string $name
     */
    protected function changeElementName($node, $name)
    {
        $ownerDocument = $node->ownerDocument;
        if (!$ownerDocument) {
            return \false;
        }
        $newNode = $ownerDocument->createElement($name);
        foreach ($node->childNodes as $child) {
            $child = $ownerDocument->importNode($child, \true);
            $newNode->appendChild($child);
        }
        foreach ($node->attributes ?? [] as $attrName => $attrNode) {
            $newNode->setAttribute($attrName, $attrNode);
        }
        if ($newNode->ownerDocument) {
            $newNode->ownerDocument->replaceChild($newNode, $node);
        }
        return $newNode;
    }
    /**
     * @param int $idx
     */
    public function childNodes($idx = -1)
    {
        $nodeList = $this->getIterator();
        if ($idx === -1) {
            return $nodeList;
        }
        return $nodeList[$idx] ?? null;
    }
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleXmlDomNodeInterface
    {
        return $this->getXmlDomParser()->findMulti($selector);
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        return $this->getXmlDomParser()->findMultiOrFalse($selector);
    }
    /**
     * @param string $selector
     */
    public function findOne($selector): SimpleXmlDomInterface
    {
        return $this->getXmlDomParser()->findOne($selector);
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        return $this->getXmlDomParser()->findOneOrFalse($selector);
    }
    public function firstChild()
    {
        $node = $this->node->firstChild;
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    /**
     * @param string $class
     */
    public function getElementByClass($class): SimpleXmlDomNodeInterface
    {
        return $this->findMulti(".{$class}");
    }
    /**
     * @param string $id
     */
    public function getElementById($id): SimpleXmlDomInterface
    {
        return $this->findOne("#{$id}");
    }
    /**
     * @param string $name
     */
    public function getElementByTagName($name): SimpleXmlDomInterface
    {
        if ($this->node instanceof DOMElement) {
            $node = $this->node->getElementsByTagName($name)->item(0);
        } else {
            $node = null;
        }
        if ($node === null) {
            return new SimpleXmlDomBlank();
        }
        return new static($node);
    }
    /**
     * @param string $id
     */
    public function getElementsById($id, $idx = null)
    {
        return $this->find("#{$id}", $idx);
    }
    /**
     * @param string $name
     */
    public function getElementsByTagName($name, $idx = null)
    {
        if ($this->node instanceof DOMElement) {
            $nodesList = $this->node->getElementsByTagName($name);
        } else {
            $nodesList = [];
        }
        $elements = new SimpleXmlDomNode();
        foreach ($nodesList as $node) {
            $elements[] = new static($node);
        }
        if ($idx === null) {
            if (\count($elements) === 0) {
                return new SimpleXmlDomNodeBlank();
            }
            return $elements;
        }
        if ($idx < 0) {
            $idx = \count($elements) + $idx;
        }
        return $elements[$idx] ?? new SimpleXmlDomBlank();
    }
    public function getNode(): DOMNode
    {
        return $this->node;
    }
    public function getXmlDomParser(): XmlDomParser
    {
        return new XmlDomParser($this);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        return $this->getXmlDomParser()->innerHtml($multiDecodeNewHtmlEntity, $putBrokenReplacedBack);
    }
    public function isRemoved(): bool
    {
        return !isset($this->node->nodeType);
    }
    public function lastChild()
    {
        $node = $this->node->lastChild;
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    public function nextSibling()
    {
        $node = $this->node->nextSibling;
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    public function nextNonWhitespaceSibling()
    {
        $node = $this->node->nextSibling;
        if ($node === null) {
            return null;
        }
        while ($node && !\trim($node->textContent)) {
            $node = $node->nextSibling;
        }
        return new static($node);
    }
    public function parentNode(): SimpleXmlDomInterface
    {
        return new static($this->node->parentNode);
    }
    public function previousSibling()
    {
        $node = $this->node->previousSibling;
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    public function previousNonWhitespaceSibling()
    {
        $node = $this->node->previousSibling;
        while ($node && !\trim($node->textContent)) {
            $node = $node->previousSibling;
        }
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    public function val($value = null)
    {
        if ($value === null) {
            if ($this->tag === 'input' && ($this->getAttribute('type') === 'hidden' || $this->getAttribute('type') === 'text' || !$this->hasAttribute('type'))) {
                return $this->getAttribute('value');
            }
            if ($this->hasAttribute('checked') && \in_array($this->getAttribute('type'), ['checkbox', 'radio'], \true)) {
                return $this->getAttribute('value');
            }
            if ($this->node->nodeName === 'select') {
                $valuesFromDom = [];
                $options = $this->getElementsByTagName('option');
                if ($options instanceof SimpleXmlDomNode) {
                    foreach ($options as $option) {
                        if ($this->hasAttribute('checked')) {
                            $valuesFromDom[] = (string) $option->getAttribute('value');
                        }
                    }
                }
                if (\count($valuesFromDom) === 0) {
                    return null;
                }
                return $valuesFromDom;
            }
            if ($this->node->nodeName === 'textarea') {
                return $this->node->nodeValue;
            }
        } else if (\in_array($this->getAttribute('type'), ['checkbox', 'radio'], \true)) {
            if ($value === $this->getAttribute('value')) {
                $this->setAttribute('checked', 'checked');
            } else {
                $this->removeAttribute('checked');
            }
        } elseif ($this->node instanceof DOMElement && $this->node->nodeName === 'select') {
            foreach ($this->node->getElementsByTagName('option') as $option) {
                if ($value === $option->getAttribute('value')) {
                    $option->setAttribute('selected', 'selected');
                } else {
                    $option->removeAttribute('selected');
                }
            }
        } elseif ($this->node->nodeName === 'input' && \is_string($value)) {
            $this->setAttribute('value', $value);
        } elseif ($this->node->nodeName === 'textarea' && \is_string($value)) {
            $this->node->nodeValue = $value;
        }
        return null;
    }
    public function getIterator(): SimpleXmlDomNodeInterface
    {
        $elements = new SimpleXmlDomNode();
        if ($this->node->hasChildNodes()) {
            foreach ($this->node->childNodes as $node) {
                $elements[] = new static($node);
            }
        }
        return $elements;
    }
    private function normalizeStringForComparision($input): string
    {
        if ($input instanceof XmlDomParser) {
            $string = $input->html(\false, \false);
        } else {
            $string = (string) $input;
        }
        return \urlencode(\urldecode(\trim(\str_replace([' ', "\n", "\r", '/>'], ['', '', '', '>'], \strtolower($string)))));
    }
}
