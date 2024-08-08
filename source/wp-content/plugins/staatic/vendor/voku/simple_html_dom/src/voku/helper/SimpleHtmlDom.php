<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use IteratorAggregate;
use DOMNode;
use BadMethodCallException;
use DOMElement;
use RuntimeException;
use DOMText;
class SimpleHtmlDom extends AbstractSimpleHtmlDom implements IteratorAggregate, SimpleHtmlDomInterface
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
        return $this->getHtmlDomParser()->find($selector, $idx);
    }
    public function getTag(): string
    {
        return $this->tag;
    }
    public function getAllAttributes()
    {
        if ($this->node && $this->node->hasAttributes()) {
            $attributes = [];
            foreach ($this->node->attributes ?? [] as $attr) {
                $attributes[$attr->name] = HtmlDomParser::putReplacedBackToPreserveHtmlEntities($attr->value);
            }
            return $attributes;
        }
        return null;
    }
    public function hasAttributes(): bool
    {
        return $this->node && $this->node->hasAttributes();
    }
    /**
     * @param string $name
     */
    public function getAttribute($name): string
    {
        if ($this->node instanceof DOMElement) {
            return HtmlDomParser::putReplacedBackToPreserveHtmlEntities($this->node->getAttribute($name));
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
    public function html($multiDecodeNewHtmlEntity = \false): string
    {
        return $this->getHtmlDomParser()->html($multiDecodeNewHtmlEntity);
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function innerHtml($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        return $this->getHtmlDomParser()->innerHtml($multiDecodeNewHtmlEntity, $putBrokenReplacedBack);
    }
    /**
     * @param string $name
     */
    public function removeAttribute($name): SimpleHtmlDomInterface
    {
        if (\method_exists($this->node, 'removeAttribute')) {
            $this->node->removeAttribute($name);
        }
        return $this;
    }
    public function removeAttributes(): SimpleHtmlDomInterface
    {
        if ($this->hasAttributes()) {
            foreach (array_keys((array) $this->getAllAttributes()) as $attribute) {
                $this->removeAttribute($attribute);
            }
        }
        return $this;
    }
    /**
     * @param string $string
     * @param bool $putBrokenReplacedBack
     */
    protected function replaceChildWithString($string, $putBrokenReplacedBack = \true): SimpleHtmlDomInterface
    {
        if (!empty($string)) {
            $newDocument = new HtmlDomParser($string);
            $tmpDomString = $this->normalizeStringForComparison($newDocument);
            $tmpStr = $this->normalizeStringForComparison($string);
            if ($tmpDomString !== $tmpStr) {
                throw new RuntimeException('Not valid HTML fragment!' . "\n" . $tmpDomString . "\n" . $tmpStr);
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
            $newDocument = $this->cleanHtmlWrapper($newDocument);
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
    protected function replaceNodeWithString($string): SimpleHtmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            $this->node = new DOMText();
            return $this;
        }
        $newDocument = new HtmlDomParser($string);
        $tmpDomOuterTextString = $this->normalizeStringForComparison($newDocument);
        $tmpStr = $this->normalizeStringForComparison($string);
        if ($tmpDomOuterTextString !== $tmpStr) {
            throw new RuntimeException('Not valid HTML fragment!' . "\n" . $tmpDomOuterTextString . "\n" . $tmpStr);
        }
        $newDocument = $this->cleanHtmlWrapper($newDocument, \true);
        $ownerDocument = $this->node->ownerDocument;
        if ($ownerDocument === null || $newDocument->getDocument()->documentElement === null) {
            return $this;
        }
        $newNode = $ownerDocument->importNode($newDocument->getDocument()->documentElement, \true);
        $this->node->parentNode->replaceChild($newNode, $this->node);
        $this->node = $newNode;
        if ($this->node->parentNode instanceof DOMElement && $newDocument->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
            $html = $this->node->parentNode->getElementsByTagName('head')[0];
            if ($html !== null && $this->node->parentNode->ownerDocument) {
                $fragment = $this->node->parentNode->ownerDocument->createDocumentFragment();
                while ($html->childNodes->length > 0) {
                    $tmpNode = $html->childNodes->item(0);
                    if ($tmpNode !== null) {
                        $fragment->appendChild($tmpNode);
                    }
                }
                $html->parentNode->replaceChild($fragment, $html);
            }
        }
        return $this;
    }
    protected function replaceTextWithString($string): SimpleHtmlDomInterface
    {
        if (empty($string)) {
            if ($this->node->parentNode) {
                $this->node->parentNode->removeChild($this->node);
            }
            $this->node = new DOMText();
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
    public function setAttribute($name, $value = null, $strictEmptyValueCheck = \false): SimpleHtmlDomInterface
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
        return $this->getHtmlDomParser()->fixHtmlOutput($this->node->textContent);
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
    public function findMulti($selector): SimpleHtmlDomNodeInterface
    {
        return $this->getHtmlDomParser()->findMulti($selector);
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        return $this->getHtmlDomParser()->findMultiOrFalse($selector);
    }
    /**
     * @param string $selector
     */
    public function findOne($selector): SimpleHtmlDomInterface
    {
        return $this->getHtmlDomParser()->findOne($selector);
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        return $this->getHtmlDomParser()->findOneOrFalse($selector);
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
    public function getElementByClass($class): SimpleHtmlDomNodeInterface
    {
        return $this->findMulti(".{$class}");
    }
    /**
     * @param string $id
     */
    public function getElementById($id): SimpleHtmlDomInterface
    {
        return $this->findOne("#{$id}");
    }
    /**
     * @param string $name
     */
    public function getElementByTagName($name): SimpleHtmlDomInterface
    {
        if ($this->node instanceof DOMElement) {
            $node = $this->node->getElementsByTagName($name)->item(0);
        } else {
            $node = null;
        }
        if ($node === null) {
            return new SimpleHtmlDomBlank();
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
        $elements = new SimpleHtmlDomNode();
        foreach ($nodesList as $node) {
            $elements[] = new static($node);
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
        return $elements[$idx] ?? new SimpleHtmlDomBlank();
    }
    public function getHtmlDomParser(): HtmlDomParser
    {
        return new HtmlDomParser($this);
    }
    public function getNode(): DOMNode
    {
        return $this->node;
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
        while ($node && !\trim($node->textContent)) {
            $node = $node->nextSibling;
        }
        if ($node === null) {
            return null;
        }
        return new static($node);
    }
    public function parentNode(): ?SimpleHtmlDomInterface
    {
        if ($node = $this->node->parentNode) {
            return new static($node);
        }
        return null;
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
                if ($options instanceof SimpleHtmlDomNode) {
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
    /**
     * @param HtmlDomParser $newDocument
     */
    protected function cleanHtmlWrapper($newDocument, $removeExtraHeadTag = \false): HtmlDomParser
    {
        if ($newDocument->getIsDOMDocumentCreatedWithoutHtml() || $newDocument->getIsDOMDocumentCreatedWithoutHtmlWrapper()) {
            if ($newDocument->getDocument()->doctype !== null) {
                $newDocument->getDocument()->doctype->parentNode->removeChild($newDocument->getDocument()->doctype);
            }
            $item = $newDocument->getDocument()->getElementsByTagName('html')->item(0);
            if ($item !== null) {
                $this->changeElementName($item, 'simpleHtmlDomHtml');
            }
            if ($newDocument->getIsDOMDocumentCreatedWithoutPTagWrapper()) {
                $pElement = $newDocument->getDocument()->getElementsByTagName('p')->item(0);
                if ($pElement instanceof DOMElement) {
                    $fragment = $newDocument->getDocument()->createDocumentFragment();
                    while ($pElement->childNodes->length > 0) {
                        $tmpNode = $pElement->childNodes->item(0);
                        if ($tmpNode !== null) {
                            $fragment->appendChild($tmpNode);
                        }
                    }
                    if ($pElement->parentNode !== null) {
                        $pElement->parentNode->replaceChild($fragment, $pElement);
                    }
                }
            }
            $body = $newDocument->getDocument()->getElementsByTagName('body')->item(0);
            if ($body instanceof DOMElement) {
                $fragment = $newDocument->getDocument()->createDocumentFragment();
                while ($body->childNodes->length > 0) {
                    $tmpNode = $body->childNodes->item(0);
                    if ($tmpNode !== null) {
                        $fragment->appendChild($tmpNode);
                    }
                }
                if ($body->parentNode !== null) {
                    $body->parentNode->replaceChild($fragment, $body);
                }
            }
        }
        if ($removeExtraHeadTag && $this->node->parentNode instanceof DOMElement && $newDocument->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
            $html = $this->node->parentNode->getElementsByTagName('head')[0] ?? null;
            if ($html !== null && $this->node->parentNode->ownerDocument) {
                $fragment = $this->node->parentNode->ownerDocument->createDocumentFragment();
                while ($html->childNodes->length > 0) {
                    $tmpNode = $html->childNodes->item(0);
                    if ($tmpNode !== null) {
                        $fragment->appendChild($tmpNode);
                    }
                }
                $html->parentNode->replaceChild($fragment, $html);
            }
        }
        return $newDocument;
    }
    public function getIterator(): SimpleHtmlDomNodeInterface
    {
        $elements = new SimpleHtmlDomNode();
        if ($this->node->hasChildNodes()) {
            foreach ($this->node->childNodes as $node) {
                $elements[] = new static($node);
            }
        }
        return $elements;
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     */
    public function innerXml($multiDecodeNewHtmlEntity = \false): string
    {
        return $this->getHtmlDomParser()->innerXml($multiDecodeNewHtmlEntity);
    }
    private function normalizeStringForComparison($input): string
    {
        if ($input instanceof HtmlDomParser) {
            $string = $input->html(\false, \false);
            if ($input->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
                $string = \str_replace(['<head>', '</head>'], '', $string);
            }
        } else {
            $string = (string) $input;
        }
        return \urlencode(\urldecode(\trim(\str_replace([' ', "\n", "\r", '/>'], ['', '', '', '>'], \strtolower($string)))));
    }
    public function delete()
    {
        $this->outertext = '';
    }
}
