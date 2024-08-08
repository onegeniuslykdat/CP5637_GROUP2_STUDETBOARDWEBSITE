<?php

namespace Staatic\Crawler;

use InvalidArgumentException;
use Staatic\Crawler\ResponseHandler\ResponseHandlerCollection;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
final class CrawlOptions
{
    public const DOM_PARSER_DOM_WRAP = 'dom_wrap';
    public const DOM_PARSER_HTML5 = 'html5';
    public const DOM_PARSER_SIMPLE_HTML = 'simple_html';
    /**
     * @var int
     */
    private $concurrency = 25;
    /**
     * @var int
     */
    private $maxRedirects = 10;
    /**
     * @var int
     */
    private $maxResponseBodyInBytes = 1024 * 1024 * 16;
    /**
     * @var int|null
     */
    private $maxCrawls;
    /**
     * @var int|null
     */
    private $maxDepth;
    /**
     * @var bool
     */
    private $forceAssets = \false;
    /**
     * @var string
     */
    private $assetsPattern = '/\.(js|css|png|jpg|gif|eot|woff|woff2|ttf|svg|webp)$/';
    /**
     * @var string
     */
    private $domParser = self::DOM_PARSER_DOM_WRAP;
    /**
     * @var ResponseHandlerCollection
     */
    private $responseFulfilledHandlers;
    /**
     * @var ResponseHandlerCollection
     */
    private $responseRejectedHandlers;
    /**
     * @var bool
     */
    private $processNotFound = \true;
    /**
     * @var bool
     */
    private $extendedUrlContext = \false;
    /**
     * @var HtmlUrlExtractorMapping
     */
    private $htmlUrlExtractorMapping;
    public function __construct(array $options = [])
    {
        if (isset($options['concurrency'])) {
            $this->setConcurrency($options['concurrency']);
        }
        if (isset($options['maxRedirects'])) {
            $this->setMaxRedirects($options['maxRedirects']);
        }
        if (isset($options['maxResponseBodyInBytes'])) {
            $this->setMaxResponseBodyInBytes($options['maxResponseBodyInBytes']);
        }
        if (isset($options['maxCrawls'])) {
            $this->setMaxCrawls($options['maxCrawls']);
        }
        if (isset($options['maxDepth'])) {
            $this->setMaxDepth($options['maxDepth']);
        }
        if (isset($options['forceAssets'])) {
            $this->setForceAssets($options['forceAssets']);
        }
        if (isset($options['assetsPattern'])) {
            $this->setAssetsPattern($options['assetsPattern']);
        }
        if (isset($options['domParser'])) {
            $this->setDomParser($options['domParser']);
        }
        $this->setResponseFulfilledHandlers($options['responseFulfilledHandlers'] ?? ResponseHandlerCollection::createDefaultFulfilledCollection());
        $this->setResponseRejectedHandlers($options['responseRejectedHandlers'] ?? ResponseHandlerCollection::createDefaultRejectedCollection());
        if (isset($options['processNotFound'])) {
            $this->setProcessNotFound($options['processNotFound']);
        }
        if (isset($options['extendedUrlContext'])) {
            $this->setExtendedUrlContext($options['extendedUrlContext']);
        }
        $this->setHtmlUrlExtractorMapping($options['htmlUrlExtractorMapping'] ?? new HtmlUrlExtractorMapping());
    }
    public function setConcurrency(int $concurrency): self
    {
        $this->concurrency = $concurrency;
        return $this;
    }
    public function concurrency(): int
    {
        return $this->concurrency;
    }
    public function setMaxRedirects(int $maxRedirects): self
    {
        $this->maxRedirects = $maxRedirects;
        return $this;
    }
    public function maxRedirects(): int
    {
        return $this->maxRedirects;
    }
    public function setMaxResponseBodyInBytes(?int $maxResponseBodyInBytes): self
    {
        $this->maxResponseBodyInBytes = $maxResponseBodyInBytes;
        return $this;
    }
    public function maxResponseBodyInBytes(): ?int
    {
        return $this->maxResponseBodyInBytes;
    }
    public function setMaxCrawls(?int $maxCrawls): self
    {
        $this->maxCrawls = $maxCrawls;
        return $this;
    }
    public function maxCrawls(): ?int
    {
        return $this->maxCrawls;
    }
    public function setMaxDepth(?int $maxDepth): self
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }
    public function maxDepth(): ?int
    {
        return $this->maxDepth;
    }
    public function setForceAssets(bool $forceAssets): self
    {
        $this->forceAssets = $forceAssets;
        return $this;
    }
    public function forceAssets(): bool
    {
        return $this->forceAssets;
    }
    public function setAssetsPattern(string $assetsPattern): self
    {
        $this->assetsPattern = $assetsPattern;
        return $this;
    }
    public function assetsPattern(): string
    {
        return $this->assetsPattern;
    }
    public function setDomParser(string $domParser): self
    {
        if (!in_array($domParser, [self::DOM_PARSER_DOM_WRAP, self::DOM_PARSER_HTML5, self::DOM_PARSER_SIMPLE_HTML], \true)) {
            throw new InvalidArgumentException("Unknown DOM parser: {$domParser}");
        }
        $this->domParser = $domParser;
        return $this;
    }
    public function domParser(): string
    {
        return $this->domParser;
    }
    public function setResponseFulfilledHandlers(ResponseHandlerCollection $handlers): self
    {
        $this->responseFulfilledHandlers = $handlers;
        return $this;
    }
    public function responseFulfilledHandlers(): ResponseHandlerCollection
    {
        return $this->responseFulfilledHandlers;
    }
    public function setResponseRejectedHandlers(ResponseHandlerCollection $handlers): self
    {
        $this->responseRejectedHandlers = $handlers;
        return $this;
    }
    public function responseRejectedHandlers(): ResponseHandlerCollection
    {
        return $this->responseRejectedHandlers;
    }
    public function setProcessNotFound(bool $processNotFound): self
    {
        $this->processNotFound = $processNotFound;
        return $this;
    }
    public function processNotFound(): bool
    {
        return $this->processNotFound;
    }
    public function setExtendedUrlContext(bool $extendedUrlContext): self
    {
        $this->extendedUrlContext = $extendedUrlContext;
        return $this;
    }
    public function extendedUrlContext(): bool
    {
        return $this->extendedUrlContext;
    }
    public function setHtmlUrlExtractorMapping(HtmlUrlExtractorMapping $mapping): self
    {
        $this->htmlUrlExtractorMapping = $mapping;
        return $this;
    }
    public function htmlUrlExtractorMapping(): HtmlUrlExtractorMapping
    {
        return $this->htmlUrlExtractorMapping;
    }
}
