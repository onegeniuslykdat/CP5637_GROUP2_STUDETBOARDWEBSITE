<?php

namespace Staatic\Crawler\UrlExtractor;

use Closure;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\DomParser\DomParserInterface;
use Staatic\Crawler\DomParser\DomWrapDomParser;
use Staatic\Crawler\UriHelper;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
final class HtmlUrlExtractor implements UrlExtractorInterface, FilterableInterface, TransformableInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var DomParserInterface
     */
    private $domParser;
    /**
     * @var UrlExtractorInterface
     */
    private $cssExtractor;
    /**
     * @var mixed[]
     */
    private $mapping;
    /**
     * @var bool
     */
    private $extendedUrlContext;
    /**
     * @var string
     */
    private $tagsExpression;
    /**
     * @var string
     */
    private $tagsSelector;
    /**
     * @var mixed[]
     */
    private $styleAttributes;
    /**
     * @var mixed[]
     */
    private $srcsetAttributes;
    /**
     * @var string
     */
    private $stylesExpression;
    /**
     * @var string
     */
    private $stylesSelector;
    /**
     * @var Closure|null
     */
    private $filterCallback;
    /**
     * @var Closure|null
     */
    private $transformCallback;
    public function __construct(DomParserInterface $domParser, ?HtmlUrlExtractorMapping $mapping = null, ?callable $filterCallback = null, ?callable $transformCallback = null, bool $extendedUrlContext = \false, ?UrlExtractorInterface $cssExtractor = null)
    {
        $this->logger = new NullLogger();
        $this->domParser = $domParser ?? new DomWrapDomParser();
        $this->cssExtractor = $cssExtractor ?? new CssUrlExtractor(null, null, $extendedUrlContext);
        $mapping = $mapping ?? new HtmlUrlExtractorMapping();
        $this->mapping = $mapping->mapping();
        $this->styleAttributes = $mapping->styleAttributes();
        $this->srcsetAttributes = $mapping->srcsetAttributes();
        $this->tagsExpression = '(//' . implode(')|(//', array_keys($this->mapping)) . ')';
        $this->tagsSelector = implode(', ', array_keys($this->mapping));
        $this->stylesExpression = '//*[@' . implode(' or @', $this->styleAttributes) . ']';
        $this->stylesSelector = '[' . implode('], [', $this->styleAttributes) . ']';
        $this->setFilterCallback($filterCallback);
        $this->setTransformCallback($transformCallback);
        $this->extendedUrlContext = $extendedUrlContext;
    }
    /**
     * @param string $content
     * @param UriInterface $baseUrl
     */
    public function extract($content, $baseUrl): Generator
    {
        $document = $this->domParser->documentFromHtml($content);
        foreach ($this->findMappedElements($document) as $element) {
            $attributes = $this->mapping[$element->localName];
            yield from $this->handleElementAttributes($element, $attributes, $baseUrl);
        }
        foreach ($this->findStyleElements($document) as $element) {
            $originalElementText = $this->domParser->getText($element);
            $generator = $this->cssExtractor->extract($originalElementText, $baseUrl);
            yield from $generator;
            $finalElementText = $generator->getReturn();
            if ($finalElementText !== $originalElementText) {
                $this->domParser->setText($element, $finalElementText);
            }
        }
        foreach ($this->findStyleAttributes($document) as $element) {
            foreach ($this->styleAttributes as $attributeName) {
                if (!$element->hasAttribute($attributeName)) {
                    continue;
                }
                $originalAttributeValue = $this->domParser->getAttribute($element, $attributeName);
                $generator = $this->cssExtractor->extract($originalAttributeValue, $baseUrl);
                yield from $generator;
                $finalAttributeValue = $generator->getReturn();
                if ($finalAttributeValue !== $originalAttributeValue) {
                    $this->domParser->setAttribute($element, $attributeName, $finalAttributeValue);
                }
            }
        }
        $newContent = $this->domParser->getHtml($document);
        if (empty($newContent)) {
            $this->logger->warning("Unable to transform HTML");
        }
        return $newContent ?: $content;
    }
    private function findMappedElements($document)
    {
        return ($document instanceof DOMDocument) ? (new DOMXPath($document))->query($this->tagsExpression) : $document->find($this->tagsSelector);
    }
    private function findStyleElements($document)
    {
        return ($document instanceof DOMDocument) ? (new DOMXPath($document))->query('//style') : $document->find('style');
    }
    private function findStyleAttributes($document)
    {
        return ($document instanceof DOMDocument) ? (new DOMXPath($document))->query($this->stylesExpression) : $document->find($this->stylesSelector);
    }
    private function handleElementAttributes($element, array $attributes, UriInterface $baseUrl): Generator
    {
        foreach ($attributes as $attributeName) {
            if (!$element->hasAttribute($attributeName)) {
                continue;
            }
            $originalAttributeValue = $this->domParser->getAttribute($element, $attributeName);
            if (in_array($attributeName, $this->srcsetAttributes)) {
                $extractedUrls = $this->extractUrlsFromSrcset($originalAttributeValue);
            } else {
                $extractedUrls = [$originalAttributeValue];
            }
            $finalAttributeValue = $originalAttributeValue;
            foreach ($extractedUrls as $extractedUrl) {
                $extractedUrl = trim($extractedUrl);
                if (!UriHelper::isReplaceableUrl($extractedUrl)) {
                    continue;
                }
                $preserveEmptyFragment = substr_compare($extractedUrl, '#', -strlen('#')) === 0;
                try {
                    $resolvedUrl = UriResolver::resolve($baseUrl, new Uri($extractedUrl));
                } catch (InvalidArgumentException $e) {
                    $this->logger->warning("Encountered an unparsable URL: '{$extractedUrl}'");
                    continue;
                }
                $context = ['extractor' => self::class, 'htmlTagName' => $element->tagName, 'htmlAttributeName' => $attributeName];
                if ($this->extendedUrlContext) {
                    $context['htmlElement'] = (string) $element;
                }
                if ($this->filterCallback && ($this->filterCallback)($resolvedUrl, $context)) {
                    $finalAttributeValue = str_replace($extractedUrl, (string) $resolvedUrl . ($preserveEmptyFragment ? '#' : ''), $finalAttributeValue);
                    continue;
                }
                $urlTransformation = $this->transformCallback ? ($this->transformCallback)($resolvedUrl, $baseUrl, $context) : new UrlTransformation($resolvedUrl);
                yield (string) $resolvedUrl => $urlTransformation->transformedUrl();
                $finalAttributeValue = str_replace($extractedUrl, (string) $urlTransformation->effectiveUrl() . ($preserveEmptyFragment ? '#' : ''), $finalAttributeValue);
            }
            if ($finalAttributeValue !== $originalAttributeValue) {
                $this->domParser->setAttribute($element, $attributeName, $finalAttributeValue);
            }
        }
    }
    private function extractUrlsFromSrcset(string $srcset): array
    {
        preg_match_all('~([^\s]+)\s*(?:[\d\.]+[wx])?,*~m', $srcset, $matches);
        return $matches[1];
    }
    /**
     * @param callable|null $callback
     */
    public function setFilterCallback($callback): void
    {
        $this->filterCallback = $callback ? Closure::fromCallable($callback) : null;
        if ($this->cssExtractor instanceof FilterableInterface) {
            $this->cssExtractor->setFilterCallback($callback);
        }
    }
    /**
     * @param callable|null $callback
     */
    public function setTransformCallback($callback): void
    {
        $this->transformCallback = $callback ? Closure::fromCallable($callback) : null;
        if ($this->cssExtractor instanceof TransformableInterface) {
            $this->cssExtractor->setTransformCallback($callback);
        }
    }
}
