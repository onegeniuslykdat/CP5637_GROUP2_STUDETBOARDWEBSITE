<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use DOMDocument;
use DOMNode;
use BadMethodCallException;
use SimpleXMLElement;
use InvalidArgumentException;
use DOMXPath;
use RuntimeException;
use Exception;
class XmlDomParser extends AbstractDomParser
{
    private $callbackXPathBeforeQuery;
    private $callbackBeforeCreateDom;
    private $autoRemoveXPathNamespaces = \false;
    private $autoRegisterXPathNamespaces = \false;
    private $reportXmlErrorsAsException = \false;
    private $xPathNamespaces = [];
    public function __construct($element = null)
    {
        $this->document = new DOMDocument('1.0', $this->getEncoding());
        $this->document->preserveWhiteSpace = \true;
        $this->document->formatOutput = \true;
        if ($element instanceof SimpleXmlDomInterface) {
            $element = $element->getNode();
        }
        if ($element instanceof DOMNode) {
            $domNode = $this->document->importNode($element, \true);
            if ($domNode instanceof DOMNode) {
                $this->document->appendChild($domNode);
            }
            return;
        }
        if ($element !== null) {
            $this->loadXml($element);
        }
    }
    public static function __callStatic($name, $arguments)
    {
        $arguments0 = $arguments[0] ?? '';
        $arguments1 = $arguments[1] ?? null;
        if ($name === 'str_get_xml') {
            $parser = new static();
            return $parser->loadXml($arguments0, $arguments1);
        }
        if ($name === 'file_get_xml') {
            $parser = new static();
            return $parser->loadXmlFile($arguments0, $arguments1);
        }
        throw new BadMethodCallException('Method does not exist');
    }
    public function __get($name)
    {
        $name = \strtolower($name);
        if ($name === 'plaintext') {
            return $this->text();
        }
        return null;
    }
    public function __toString()
    {
        return $this->xml(\false, \false, \true, 0);
    }
    /**
     * @param string $xml
     */
    protected function createDOMDocument($xml, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): DOMDocument
    {
        if ($this->callbackBeforeCreateDom) {
            $xml = \call_user_func($this->callbackBeforeCreateDom, $xml, $this);
        }
        $internalErrors = \libxml_use_internal_errors(\true);
        if (\PHP_VERSION_ID < 80000) {
            $disableEntityLoader = \libxml_disable_entity_loader(\true);
        }
        \libxml_clear_errors();
        $optionsXml = 0;
        if ($useDefaultLibXMLOptions) {
            $optionsXml = \LIBXML_DTDLOAD | \LIBXML_DTDATTR | \LIBXML_NONET;
            if (\defined('LIBXML_BIGLINES')) {
                $optionsXml |= \LIBXML_BIGLINES;
            }
            if (\defined('LIBXML_COMPACT')) {
                $optionsXml |= \LIBXML_COMPACT;
            }
        }
        if ($libXMLExtraOptions !== null) {
            $optionsXml |= $libXMLExtraOptions;
        }
        $this->xPathNamespaces = [];
        $matches = [];
        \preg_match_all('#xmlns:(?<namespaceKey>.*)=(["\'])(?<namespaceValue>.*)\2#Ui', $xml, $matches);
        foreach ($matches['namespaceKey'] ?? [] as $index => $key) {
            if ($key) {
                $this->xPathNamespaces[\trim($key, ':')] = $matches['namespaceValue'][$index];
            }
        }
        if ($this->autoRemoveXPathNamespaces) {
            $xml = $this->removeXPathNamespaces($xml);
        }
        $xml = self::replaceToPreserveHtmlEntities($xml);
        $documentFound = \false;
        $sxe = \simplexml_load_string($xml, SimpleXMLElement::class, $optionsXml);
        $xmlErrors = \libxml_get_errors();
        if ($sxe !== \false && \count($xmlErrors) === 0) {
            $domElementTmp = \dom_import_simplexml($sxe);
            if ($domElementTmp->ownerDocument instanceof DOMDocument) {
                $documentFound = \true;
                $this->document = $domElementTmp->ownerDocument;
            }
        }
        if ($documentFound === \false) {
            $xmlHackUsed = \false;
            if (\stripos('<?xml', $xml) !== 0) {
                $xmlHackUsed = \true;
                $xml = '<?xml encoding="' . $this->getEncoding() . '" ?>' . $xml;
            }
            $documentFound = $this->document->loadXML($xml, $optionsXml);
            if ($xmlHackUsed) {
                foreach ($this->document->childNodes as $child) {
                    if ($child->nodeType === \XML_PI_NODE) {
                        $this->document->removeChild($child);
                        break;
                    }
                }
            }
        }
        if ($documentFound === \false && \count($xmlErrors) > 0) {
            $errorStr = 'XML-Errors: ' . \print_r($xmlErrors, \true) . ' in ' . \print_r($xml, \true);
            if (!$this->reportXmlErrorsAsException) {
                \trigger_error($errorStr, \E_USER_WARNING);
            } else {
                throw new InvalidArgumentException($errorStr);
            }
        }
        $this->document->encoding = $this->getEncoding();
        \libxml_clear_errors();
        \libxml_use_internal_errors($internalErrors);
        if (\PHP_VERSION_ID < 80000 && isset($disableEntityLoader)) {
            \libxml_disable_entity_loader($disableEntityLoader);
        }
        return $this->document;
    }
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null)
    {
        $xPathQuery = SelectorConverter::toXPath($selector, \true, \false);
        $xPath = new DOMXPath($this->document);
        if ($this->autoRegisterXPathNamespaces) {
            foreach ($this->xPathNamespaces as $key => $value) {
                $xPath->registerNamespace($key, $value);
            }
        }
        if ($this->callbackXPathBeforeQuery) {
            $xPathQuery = \call_user_func($this->callbackXPathBeforeQuery, $selector, $xPathQuery, $xPath, $this);
        }
        $nodesList = $xPath->query($xPathQuery);
        $elements = new SimpleXmlDomNode();
        if ($nodesList) {
            foreach ($nodesList as $node) {
                $elements[] = new SimpleXmlDom($node);
            }
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
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleXmlDomNodeInterface
    {
        return $this->find($selector, null);
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        $return = $this->find($selector, null);
        if ($return instanceof SimpleXmlDomNodeBlank) {
            return \false;
        }
        return $return;
    }
    /**
     * @param string $selector
     */
    public function findOne($selector): SimpleXmlDomInterface
    {
        return $this->find($selector, 0);
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        $return = $this->find($selector, 0);
        if ($return instanceof SimpleXmlDomBlank) {
            return \false;
        }
        return $return;
    }
    /**
     * @param string $content
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function fixHtmlOutput($content, $multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        $content = $this->decodeHtmlEntity($content, $multiDecodeNewHtmlEntity);
        return self::putReplacedBackToPreserveHtmlEntities($content, $putBrokenReplacedBack);
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
        $node = $this->document->getElementsByTagName($name)->item(0);
        if ($node === null) {
            return new SimpleXmlDomBlank();
        }
        return new SimpleXmlDom($node);
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
        $nodesList = $this->document->getElementsByTagName($name);
        $elements = new SimpleXmlDomNode();
        foreach ($nodesList as $node) {
            $elements[] = new SimpleXmlDom($node);
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
        return $elements[$idx] ?? new SimpleXmlDomNodeBlank();
    }
    /**
     * @param bool $multiDecodeNewHtmlEntity
     * @param bool $putBrokenReplacedBack
     */
    public function html($multiDecodeNewHtmlEntity = \false, $putBrokenReplacedBack = \true): string
    {
        if (static::$callback !== null) {
            \call_user_func(static::$callback, [$this]);
        }
        $content = $this->document->saveHTML();
        if ($content === \false) {
            return '';
        }
        return $this->fixHtmlOutput($content, $multiDecodeNewHtmlEntity, $putBrokenReplacedBack);
    }
    /**
     * @param string $html
     */
    public function loadHtml($html, $libXMLExtraOptions = null): DomParserInterface
    {
        $this->document = $this->createDOMDocument($html, $libXMLExtraOptions);
        return $this;
    }
    /**
     * @param string $filePath
     */
    public function loadHtmlFile($filePath, $libXMLExtraOptions = null): DomParserInterface
    {
        if (!\preg_match("/^https?:\\/\\//i", $filePath) && !\file_exists($filePath)) {
            throw new RuntimeException("File {$filePath} not found");
        }
        try {
            if (\class_exists('Staatic\Vendor\voku\helper\UTF8')) {
                $html = UTF8::file_get_contents($filePath);
            } else {
                $html = \file_get_contents($filePath);
            }
        } catch (Exception $e) {
            throw new RuntimeException("Could not load file {$filePath}");
        }
        if ($html === \false) {
            throw new RuntimeException("Could not load file {$filePath}");
        }
        return $this->loadHtml($html, $libXMLExtraOptions);
    }
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }
    private function removeXPathNamespaces(string $xml): string
    {
        foreach ($this->xPathNamespaces as $key => $value) {
            $xml = \str_replace($key . ':', '', $xml);
        }
        return (string) \preg_replace('#xmlns:?.*=(["\'])(?:.*)\1#Ui', '', $xml);
    }
    /**
     * @param string $xml
     */
    public function loadXml($xml, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): self
    {
        $this->document = $this->createDOMDocument($xml, $libXMLExtraOptions, $useDefaultLibXMLOptions);
        return $this;
    }
    /**
     * @param string $filePath
     */
    public function loadXmlFile($filePath, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): self
    {
        if (!\preg_match("/^https?:\\/\\//i", $filePath) && !\file_exists($filePath)) {
            throw new RuntimeException("File {$filePath} not found");
        }
        try {
            if (\class_exists('Staatic\Vendor\voku\helper\UTF8')) {
                $xml = UTF8::file_get_contents($filePath);
            } else {
                $xml = \file_get_contents($filePath);
            }
        } catch (Exception $e) {
            throw new RuntimeException("Could not load file {$filePath}");
        }
        if ($xml === \false) {
            throw new RuntimeException("Could not load file {$filePath}");
        }
        return $this->loadXml($xml, $libXMLExtraOptions, $useDefaultLibXMLOptions);
    }
    /**
     * @param DOMNode|null $domNode
     */
    public function replaceTextWithCallback($callback, $domNode = null)
    {
        if ($domNode === null) {
            $domNode = $this->document;
        }
        if ($domNode->hasChildNodes()) {
            $children = [];
            foreach ($domNode->childNodes as $child) {
                $children[] = $child;
            }
            foreach ($children as $child) {
                if ($child->nodeType === \XML_TEXT_NODE) {
                    $child = $child;
                    $oldText = self::putReplacedBackToPreserveHtmlEntities($child->wholeText);
                    $newText = $callback($oldText);
                    if ($domNode->ownerDocument) {
                        $newTextNode = $domNode->ownerDocument->createTextNode(self::replaceToPreserveHtmlEntities($newText));
                        $domNode->replaceChild($newTextNode, $child);
                    }
                } else {
                    $this->replaceTextWithCallback($callback, $child);
                }
            }
        }
    }
    /**
     * @param bool $autoRemoveXPathNamespaces
     */
    public function autoRemoveXPathNamespaces($autoRemoveXPathNamespaces = \true): self
    {
        $this->autoRemoveXPathNamespaces = $autoRemoveXPathNamespaces;
        return $this;
    }
    /**
     * @param bool $autoRegisterXPathNamespaces
     */
    public function autoRegisterXPathNamespaces($autoRegisterXPathNamespaces = \true): self
    {
        $this->autoRegisterXPathNamespaces = $autoRegisterXPathNamespaces;
        return $this;
    }
    /**
     * @param callable $callbackXPathBeforeQuery
     */
    public function setCallbackXPathBeforeQuery($callbackXPathBeforeQuery): self
    {
        $this->callbackXPathBeforeQuery = $callbackXPathBeforeQuery;
        return $this;
    }
    /**
     * @param callable $callbackBeforeCreateDom
     */
    public function setCallbackBeforeCreateDom($callbackBeforeCreateDom): self
    {
        $this->callbackBeforeCreateDom = $callbackBeforeCreateDom;
        return $this;
    }
    /**
     * @param bool $reportXmlErrorsAsException
     */
    public function reportXmlErrorsAsException($reportXmlErrorsAsException = \true): self
    {
        $this->reportXmlErrorsAsException = $reportXmlErrorsAsException;
        return $this;
    }
}
