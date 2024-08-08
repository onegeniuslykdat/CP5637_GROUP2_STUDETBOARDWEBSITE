<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use DOMDocument;
use DOMNode;
use BadMethodCallException;
use SimpleXMLElement;
use DOMXPath;
use RuntimeException;
use Exception;
use InvalidArgumentException;
class HtmlDomParser extends AbstractDomParser
{
    private $callbackXPathBeforeQuery;
    private $callbackBeforeCreateDom;
    protected static $functionAliases = ['outertext' => 'html', 'outerhtml' => 'html', 'innertext' => 'innerHtml', 'innerhtml' => 'innerHtml', 'load' => 'loadHtml', 'load_file' => 'loadHtmlFile'];
    protected $templateLogicSyntaxInSpecialScriptTags = ['+', '<%', '{%', '{{'];
    protected $specialScriptTags = ['text/html', 'text/template', 'text/x-custom-template', 'text/x-handlebars-template'];
    protected $selfClosingTags = ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
    protected $isDOMDocumentCreatedWithoutHtml = \false;
    protected $isDOMDocumentCreatedWithoutWrapper = \false;
    protected $isDOMDocumentCreatedWithCommentWrapper = \false;
    protected $isDOMDocumentCreatedWithoutHeadWrapper = \false;
    protected $isDOMDocumentCreatedWithoutPTagWrapper = \false;
    protected $isDOMDocumentCreatedWithoutHtmlWrapper = \false;
    protected $isDOMDocumentCreatedWithoutBodyWrapper = \false;
    protected $isDOMDocumentCreatedWithMultiRoot = \false;
    protected $isDOMDocumentCreatedWithFakeEndScript = \false;
    protected $keepBrokenHtml = \false;
    public function __construct($element = null)
    {
        $this->document = new DOMDocument('1.0', $this->getEncoding());
        $this->document->preserveWhiteSpace = \true;
        $this->document->formatOutput = \true;
        if ($element instanceof SimpleHtmlDomInterface) {
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
            $this->loadHtml($element);
        }
    }
    public function __call($name, $arguments)
    {
        $name = \strtolower($name);
        if (isset(self::$functionAliases[$name])) {
            return \call_user_func_array([$this, self::$functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist: ' . $name);
    }
    public static function __callStatic($name, $arguments)
    {
        $arguments0 = $arguments[0] ?? '';
        $arguments1 = $arguments[1] ?? null;
        if ($name === 'str_get_html') {
            $parser = new static();
            return $parser->loadHtml($arguments0, $arguments1);
        }
        if ($name === 'file_get_html') {
            $parser = new static();
            return $parser->loadHtmlFile($arguments0, $arguments1);
        }
        throw new BadMethodCallException('Method does not exist');
    }
    public function __get($name)
    {
        $name = \strtolower($name);
        switch ($name) {
            case 'outerhtml':
            case 'outertext':
                return $this->html();
            case 'innerhtml':
            case 'innertext':
                return $this->innerHtml();
            case 'innerhtmlkeep':
                return $this->innerHtml(\false, \false);
            case 'text':
            case 'plaintext':
                return $this->text();
        }
        return null;
    }
    public function __toString()
    {
        return $this->html();
    }
    public function clear(): bool
    {
        return \true;
    }
    /**
     * @param string $html
     */
    protected function createDOMDocument($html, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): DOMDocument
    {
        if ($this->callbackBeforeCreateDom) {
            $html = \call_user_func($this->callbackBeforeCreateDom, $html, $this);
        }
        $isDOMDocumentCreatedWithDoctype = \false;
        if (\stripos($html, '<!DOCTYPE') !== \false) {
            $isDOMDocumentCreatedWithDoctype = \true;
            if (\preg_match('/(^.*?)<!DOCTYPE(?: [^>]*)?>/sui', $html, $matches_before_doctype) && \trim($matches_before_doctype[1])) {
                $html = \str_replace($matches_before_doctype[1], '', $html);
            }
        }
        if ($this->keepBrokenHtml) {
            $html = $this->keepBrokenHtml(\trim($html));
        }
        if (\strpos($html, '<') === \false) {
            $this->isDOMDocumentCreatedWithoutHtml = \true;
        } elseif (\strpos(\ltrim($html), '<') !== 0) {
            $this->isDOMDocumentCreatedWithoutWrapper = \true;
        }
        if (\strpos(\ltrim($html), '<!--') === 0) {
            $this->isDOMDocumentCreatedWithCommentWrapper = \true;
        }
        if (\strpos($html, '<html ') === \false && \strpos($html, '<html>') === \false) {
            $this->isDOMDocumentCreatedWithoutHtmlWrapper = \true;
        }
        if (\strpos($html, '<body ') === \false && \strpos($html, '<body>') === \false) {
            $this->isDOMDocumentCreatedWithoutBodyWrapper = \true;
        }
        if (\strpos($html, '<head ') === \false && \strpos($html, '<head>') === \false) {
            $this->isDOMDocumentCreatedWithoutHeadWrapper = \true;
        }
        if (\strpos($html, '<p ') === \false && \strpos($html, '<p>') === \false) {
            $this->isDOMDocumentCreatedWithoutPTagWrapper = \true;
        }
        if (\strpos($html, '</script>') === \false && \strpos($html, '<\/script>') !== \false) {
            $this->isDOMDocumentCreatedWithFakeEndScript = \true;
        }
        if (\stripos($html, '</html>') !== \false) {
            if (\preg_match('/<\/html>(.*?)/suiU', $html, $matches_after_html) && \trim($matches_after_html[1])) {
                $html = \str_replace($matches_after_html[0], $matches_after_html[1] . '</html>', $html);
            }
        }
        if (\strpos($html, '<script') !== \false) {
            $this->html5FallbackForScriptTags($html);
            foreach ($this->specialScriptTags as $tag) {
                if (\strpos($html, $tag) !== \false) {
                    $this->keepSpecialScriptTags($html);
                }
            }
        }
        if (\strpos($html, '<svg') !== \false) {
            $this->keepSpecialSvgTags($html);
        }
        if ($this->isDOMDocumentCreatedWithoutHtmlWrapper && $this->isDOMDocumentCreatedWithoutBodyWrapper) {
            if (\substr_count($html, '</') >= 2) {
                $regexForMultiRootDetection = '#<(.*)>.*?</(\1)>#su';
                \preg_match($regexForMultiRootDetection, $html, $matches);
                if (($matches[0] ?? '') !== $html) {
                    $htmlTmp = \preg_replace($regexForMultiRootDetection, '', $html);
                    if ($htmlTmp !== null && trim($htmlTmp) === '') {
                        $this->isDOMDocumentCreatedWithMultiRoot = \true;
                    }
                }
            }
        }
        $html = \str_replace(\array_map(static function ($e) {
            return '<' . $e . '>';
        }, $this->selfClosingTags), \array_map(static function ($e) {
            return '<' . $e . '/>';
        }, $this->selfClosingTags), $html);
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
            if (\defined('LIBXML_HTML_NODEFDTD')) {
                $optionsXml |= \LIBXML_HTML_NODEFDTD;
            }
        }
        if ($libXMLExtraOptions !== null) {
            $optionsXml |= $libXMLExtraOptions;
        }
        if ($this->isDOMDocumentCreatedWithMultiRoot || $this->isDOMDocumentCreatedWithoutWrapper || $this->isDOMDocumentCreatedWithCommentWrapper || !$isDOMDocumentCreatedWithDoctype && $this->keepBrokenHtml) {
            $html = '<' . self::$domHtmlWrapperHelper . '>' . $html . '</' . self::$domHtmlWrapperHelper . '>';
        }
        $html = self::replaceToPreserveHtmlEntities($html);
        $documentFound = \false;
        $sxe = \simplexml_load_string($html, SimpleXMLElement::class, $optionsXml);
        if ($sxe !== \false && \count(\libxml_get_errors()) === 0) {
            $domElementTmp = \dom_import_simplexml($sxe);
            if ($domElementTmp->ownerDocument instanceof DOMDocument) {
                $documentFound = \true;
                $this->document = $domElementTmp->ownerDocument;
            }
        }
        if ($documentFound === \false) {
            $xmlHackUsed = \false;
            if (\stripos('<?xml', $html) !== 0) {
                $xmlHackUsed = \true;
                $html = '<?xml encoding="' . $this->getEncoding() . '" ?>' . $html;
            }
            if ($html !== '') {
                $this->document->loadHTML($html, $optionsXml);
            }
            if ($xmlHackUsed) {
                foreach ($this->document->childNodes as $child) {
                    if ($child->nodeType === \XML_PI_NODE) {
                        $this->document->removeChild($child);
                        break;
                    }
                }
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
        $xPathQuery = SelectorConverter::toXPath($selector);
        $xPath = new DOMXPath($this->document);
        if ($this->callbackXPathBeforeQuery) {
            $xPathQuery = \call_user_func($this->callbackXPathBeforeQuery, $selector, $xPathQuery, $xPath, $this);
        }
        $nodesList = $xPath->query($xPathQuery);
        $elements = new SimpleHtmlDomNode();
        if ($nodesList) {
            foreach ($nodesList as $node) {
                $elements[] = new SimpleHtmlDom($node);
            }
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
    /**
     * @param string $selector
     */
    public function findMulti($selector): SimpleHtmlDomNodeInterface
    {
        return $this->find($selector, null);
    }
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector)
    {
        $return = $this->find($selector, null);
        if ($return instanceof SimpleHtmlDomNodeBlank) {
            return \false;
        }
        return $return;
    }
    /**
     * @param string $selector
     */
    public function findOne($selector): SimpleHtmlDomInterface
    {
        return $this->find($selector, 0);
    }
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector)
    {
        $return = $this->find($selector, 0);
        if ($return instanceof SimpleHtmlDomBlank) {
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
        if ($this->getIsDOMDocumentCreatedWithoutHtmlWrapper()) {
            $content = \str_replace(['<html>', '</html>'], '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithoutHeadWrapper()) {
            $content = \str_replace(['<head>', '</head>'], '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithoutBodyWrapper()) {
            $content = \str_replace(['<body>', '</body>'], '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithFakeEndScript()) {
            $content = \str_replace('</script>', '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithoutWrapper()) {
            $content = (string) \preg_replace('/^<p>/', '', $content);
            $content = (string) \preg_replace('/<\/p>/', '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithoutPTagWrapper()) {
            $content = \str_replace(['<p>', '</p>'], '', $content);
        }
        if ($this->getIsDOMDocumentCreatedWithoutHtml()) {
            $content = \str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', '', $content);
        }
        $content = \str_replace(\array_map(static function ($e) {
            return '</' . $e . '>';
        }, $this->selfClosingTags), '', $content);
        $content = \trim(\str_replace(['<simpleHtmlDomHtml>', '</simpleHtmlDomHtml>', '<simpleHtmlDomP>', '</simpleHtmlDomP>', '<head><head>', '</head></head>'], ['', '', '', '', '<head>', '</head>'], $content));
        $content = $this->decodeHtmlEntity($content, $multiDecodeNewHtmlEntity);
        return self::putReplacedBackToPreserveHtmlEntities($content, $putBrokenReplacedBack);
    }
    /**
     * @param string $class
     */
    public function getElementByClass($class): SimpleHtmlDomNodeInterface
    {
        return $this->findMulti('.' . $class);
    }
    /**
     * @param string $id
     */
    public function getElementById($id): SimpleHtmlDomInterface
    {
        return $this->findOne('#' . $id);
    }
    /**
     * @param string $name
     */
    public function getElementByTagName($name): SimpleHtmlDomInterface
    {
        $node = $this->document->getElementsByTagName($name)->item(0);
        if ($node === null) {
            return new SimpleHtmlDomBlank();
        }
        return new SimpleHtmlDom($node);
    }
    /**
     * @param string $id
     */
    public function getElementsById($id, $idx = null)
    {
        return $this->find('#' . $id, $idx);
    }
    /**
     * @param string $name
     */
    public function getElementsByTagName($name, $idx = null)
    {
        $nodesList = $this->document->getElementsByTagName($name);
        $elements = new SimpleHtmlDomNode();
        foreach ($nodesList as $node) {
            $elements[] = new SimpleHtmlDom($node);
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
        return $elements[$idx] ?? new SimpleHtmlDomNodeBlank();
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
        if ($this->getIsDOMDocumentCreatedWithoutHtmlWrapper()) {
            $content = $this->document->saveHTML($this->document->documentElement);
        } else {
            $content = $this->document->saveHTML();
        }
        if ($content === \false) {
            return '';
        }
        return $this->fixHtmlOutput($content, $multiDecodeNewHtmlEntity, $putBrokenReplacedBack);
    }
    /**
     * @param string $html
     */
    public function loadHtml($html, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): DomParserInterface
    {
        $this->document = $this->createDOMDocument($html, $libXMLExtraOptions, $useDefaultLibXMLOptions);
        return $this;
    }
    /**
     * @param string $filePath
     */
    public function loadHtmlFile($filePath, $libXMLExtraOptions = null, $useDefaultLibXMLOptions = \true): DomParserInterface
    {
        if (!\preg_match("/^https?:\\/\\//i", $filePath) && !\file_exists($filePath)) {
            throw new RuntimeException('File ' . $filePath . ' not found');
        }
        try {
            if (\class_exists('Staatic\Vendor\voku\helper\UTF8')) {
                $html = UTF8::file_get_contents($filePath);
            } else {
                $html = \file_get_contents($filePath);
            }
        } catch (Exception $e) {
            throw new RuntimeException('Could not load file ' . $filePath);
        }
        if ($html === \false) {
            throw new RuntimeException('Could not load file ' . $filePath);
        }
        return $this->loadHtml($html, $libXMLExtraOptions, $useDefaultLibXMLOptions);
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
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }
    public function getIsDOMDocumentCreatedWithoutHeadWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHeadWrapper;
    }
    public function getIsDOMDocumentCreatedWithoutPTagWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutPTagWrapper;
    }
    public function getIsDOMDocumentCreatedWithoutHtml(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHtml;
    }
    public function getIsDOMDocumentCreatedWithoutBodyWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutBodyWrapper;
    }
    public function getIsDOMDocumentCreatedWithMultiRoot(): bool
    {
        return $this->isDOMDocumentCreatedWithMultiRoot;
    }
    public function getIsDOMDocumentCreatedWithoutHtmlWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutHtmlWrapper;
    }
    public function getIsDOMDocumentCreatedWithoutWrapper(): bool
    {
        return $this->isDOMDocumentCreatedWithoutWrapper;
    }
    public function getIsDOMDocumentCreatedWithFakeEndScript(): bool
    {
        return $this->isDOMDocumentCreatedWithFakeEndScript;
    }
    /**
     * @param string $html
     */
    protected function keepBrokenHtml($html): string
    {
        do {
            $original = $html;
            $html = (string) \preg_replace_callback('/(?<start>.*)<(?<element_start>[a-z]+)(?<element_start_addon> [^>]*)?>(?<value>.*?)<\/(?<element_end>\2)>(?<end>.*)/sui', static function ($matches) {
                return $matches['start'] . '°lt_simple_html_dom__voku_°' . $matches['element_start'] . $matches['element_start_addon'] . '°gt_simple_html_dom__voku_°' . $matches['value'] . '°lt/_simple_html_dom__voku_°' . $matches['element_end'] . '°gt_simple_html_dom__voku_°' . $matches['end'];
            }, $html);
        } while ($original !== $html);
        do {
            $original = $html;
            $html = (string) \preg_replace_callback('/(?<start>[^<]*)?(?<broken>(?:<\/\w+(?:\s+\w+=\"[^"]+\")*+[^<]+>)+)(?<end>.*)/u', static function ($matches) {
                $matches['broken'] = \str_replace(['°lt/_simple_html_dom__voku_°', '°lt_simple_html_dom__voku_°', '°gt_simple_html_dom__voku_°'], ['</', '<', '>'], $matches['broken']);
                self::$domBrokenReplaceHelper['orig'][] = $matches['broken'];
                self::$domBrokenReplaceHelper['tmp'][] = $matchesHash = self::$domHtmlBrokenHtmlHelper . \crc32($matches['broken']);
                return $matches['start'] . $matchesHash . $matches['end'];
            }, $html);
        } while ($original !== $html);
        return \str_replace(['°lt/_simple_html_dom__voku_°', '°lt_simple_html_dom__voku_°', '°gt_simple_html_dom__voku_°'], ['</', '<', '>'], $html);
    }
    /**
     * @param string $html
     */
    protected function keepSpecialSvgTags(&$html)
    {
        $regExSpecialSvg = '/\((["\'])?(?<start>data:image\/svg.*)<svg(?<attr>[^>]*?)>(?<content>.*)<\/svg>\1\)/isU';
        $htmlTmp = \preg_replace_callback($regExSpecialSvg, static function ($svgs) {
            if (empty($svgs['content'])) {
                return $svgs[0];
            }
            $content = '<svg' . $svgs['attr'] . '>' . $svgs['content'] . '</svg>';
            self::$domBrokenReplaceHelper['orig'][] = $content;
            self::$domBrokenReplaceHelper['tmp'][] = $matchesHash = self::$domHtmlBrokenHtmlHelper . \crc32($content);
            return '(' . $svgs[1] . $svgs['start'] . $matchesHash . $svgs[1] . ')';
        }, $html);
        if ($htmlTmp !== null) {
            $html = $htmlTmp;
        }
    }
    /**
     * @param string $html
     */
    protected function keepSpecialScriptTags(&$html)
    {
        $tags = \implode('|', \array_map(static function ($value) {
            return \preg_quote($value, '/');
        }, $this->specialScriptTags));
        $html = (string) \preg_replace_callback('/(?<start>(<script [^>]*type=["\']?(?:' . $tags . ')+[^>]*>))(?<innerContent>.*)(?<end><\/script>)/isU', function ($matches) {
            foreach ($this->templateLogicSyntaxInSpecialScriptTags as $logicSyntaxInSpecialScriptTag) {
                if (\strpos($matches['innerContent'], $logicSyntaxInSpecialScriptTag) !== \false) {
                    $matches['innerContent'] = \str_replace('<\/', '</', $matches['innerContent']);
                    self::$domBrokenReplaceHelper['orig'][] = $matches['innerContent'];
                    self::$domBrokenReplaceHelper['tmp'][] = $matchesHash = self::$domHtmlBrokenHtmlHelper . \crc32($matches['innerContent']);
                    return $matches['start'] . $matchesHash . $matches['end'];
                }
            }
            $matches[0] = \str_replace('<\/', '</', $matches[0]);
            $specialNonScript = '<' . self::$domHtmlSpecialScriptHelper . \substr($matches[0], \strlen('<script'));
            return \substr($specialNonScript, 0, -\strlen('</script>')) . '</' . self::$domHtmlSpecialScriptHelper . '>';
        }, $html);
    }
    /**
     * @param bool $keepBrokenHtml
     */
    public function useKeepBrokenHtml($keepBrokenHtml): DomParserInterface
    {
        $this->keepBrokenHtml = $keepBrokenHtml;
        return $this;
    }
    /**
     * @param mixed[] $templateLogicSyntaxInSpecialScriptTags
     */
    public function overwriteTemplateLogicSyntaxInSpecialScriptTags($templateLogicSyntaxInSpecialScriptTags): DomParserInterface
    {
        foreach ($templateLogicSyntaxInSpecialScriptTags as $tmp) {
            if (!\is_string($tmp)) {
                throw new InvalidArgumentException('setTemplateLogicSyntaxInSpecialScriptTags only allows string[]');
            }
        }
        $this->templateLogicSyntaxInSpecialScriptTags = $templateLogicSyntaxInSpecialScriptTags;
        return $this;
    }
    /**
     * @param mixed[] $specialScriptTags
     */
    public function overwriteSpecialScriptTags($specialScriptTags): DomParserInterface
    {
        foreach ($specialScriptTags as $tag) {
            if (!\is_string($tag)) {
                throw new InvalidArgumentException('SpecialScriptTags only allows string[]');
            }
        }
        $this->specialScriptTags = $specialScriptTags;
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
}
