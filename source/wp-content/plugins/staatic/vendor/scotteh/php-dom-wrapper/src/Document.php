<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap;

use DOMDocument;
use Staatic\Vendor\DOMWrap\Traits\CommonTrait;
use Staatic\Vendor\DOMWrap\Traits\TraversalTrait;
use Staatic\Vendor\DOMWrap\Traits\ManipulationTrait;
class Document extends DOMDocument
{
    use CommonTrait;
    use TraversalTrait;
    use ManipulationTrait;
    protected $libxmlOptions = 0;
    protected $documentEncoding = null;
    public function __construct(string $version = '1.0', string $encoding = 'UTF-8')
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMText', 'Staatic\Vendor\DOMWrap\Text');
        $this->registerNodeClass('DOMElement', 'Staatic\Vendor\DOMWrap\Element');
        $this->registerNodeClass('DOMComment', 'Staatic\Vendor\DOMWrap\Comment');
        $this->registerNodeClass('DOMDocument', 'Staatic\Vendor\DOMWrap\Document');
        $this->registerNodeClass('DOMDocumentType', 'Staatic\Vendor\DOMWrap\DocumentType');
        $this->registerNodeClass('DOMProcessingInstruction', 'Staatic\Vendor\DOMWrap\ProcessingInstruction');
    }
    /**
     * @param int $libxmlOptions
     */
    public function setLibxmlOptions($libxmlOptions): void
    {
        $this->libxmlOptions = $libxmlOptions;
    }
    public function document(): ?DOMDocument
    {
        return $this;
    }
    public function collection(): NodeList
    {
        return $this->newNodeList([$this]);
    }
    /**
     * @param NodeList $nodeList
     */
    public function result($nodeList)
    {
        if ($nodeList->count()) {
            return $nodeList->first();
        }
        return null;
    }
    public function parent()
    {
        return null;
    }
    public function parents()
    {
        return $this->newNodeList();
    }
    public function substituteWith($newNode): self
    {
        $this->replaceChild($newNode, $this);
        return $this;
    }
    public function _clone()
    {
        return null;
    }
    public function getHtml(): string
    {
        return $this->getOuterHtml();
    }
    public function setHtml($html): self
    {
        if (!is_string($html) || trim($html) == '') {
            return $this;
        }
        $internalErrors = libxml_use_internal_errors(\true);
        if (\PHP_VERSION_ID < 80000) {
            $disableEntities = libxml_disable_entity_loader(\true);
            $this->composeXmlNode($html);
            libxml_use_internal_errors($internalErrors);
            libxml_disable_entity_loader($disableEntities);
        } else {
            $this->composeXmlNode($html);
            libxml_use_internal_errors($internalErrors);
        }
        return $this;
    }
    public function loadHTML($html, $options = 0): bool
    {
        if ($options & \LIBXML_HTML_NOIMPLIED) {
            $html = '<domwrap></domwrap>' . $html;
        }
        $html = '<?xml encoding="' . ($this->getEncoding() ?? 'UTF-8') . '">' . $html;
        $result = parent::loadHTML($html, $options);
        if ($this->libxmlOptions & \LIBXML_HTML_NOIMPLIED) {
            $this->children()->first()->contents()->each(function ($node) {
                $this->appendWith($node);
            });
            $this->removeChild($this->children()->first());
        }
        return $result;
    }
    /**
     * @param string|null $encoding
     */
    public function setEncoding($encoding = null)
    {
        $this->documentEncoding = $encoding;
    }
    public function getEncoding(): ?string
    {
        return $this->documentEncoding;
    }
    private function getCharset(string $html): ?string
    {
        $charset = null;
        if (preg_match('@<meta[^>]*?charset=["\']?([^"\'\s>]+)@im', $html, $matches)) {
            $charset = mb_strtoupper($matches[1]);
        }
        return $charset;
    }
    private function detectEncoding(string $html)
    {
        $charset = $this->getEncoding();
        if (is_null($charset)) {
            $charset = $this->getCharset($html);
        }
        $detectedCharset = mb_detect_encoding($html, mb_detect_order(), \true);
        if ($charset === null && $detectedCharset == 'UTF-8') {
            $charset = $detectedCharset;
        }
        $this->setEncoding($charset);
    }
    private function convertToUtf8(string $html): string
    {
        $charset = $this->getEncoding();
        if ($charset !== null) {
            $html = preg_replace('@(charset=["]?)([^"\s]+)([^"]*["]?)@im', '$1UTF-8$3', $html);
            $mbHasCharset = in_array($charset, array_map('mb_strtoupper', mb_list_encodings()));
            if ($mbHasCharset) {
                $html = mb_convert_encoding($html, 'UTF-8', $charset);
            } elseif (extension_loaded('iconv')) {
                $htmlIconv = iconv($charset, 'UTF-8', $html);
                if ($htmlIconv !== \false) {
                    $html = $htmlIconv;
                } else {
                    $charset = null;
                }
            }
        }
        if ($charset === null) {
            $html = htmlspecialchars_decode(mb_encode_numericentity(htmlentities($html, \ENT_QUOTES, 'UTF-8'), [0x80, 0x10ffff, 0, ~0], 'UTF-8'));
        }
        return $html;
    }
    private function composeXmlNode($html)
    {
        $this->detectEncoding($html);
        $html = $this->convertToUtf8($html);
        $this->loadHTML($html, $this->libxmlOptions);
        $this->contents()->each(function ($node) {
            if ($node instanceof ProcessingInstruction && $node->nodeName == 'xml') {
                $node->destroy();
            }
        });
    }
}
