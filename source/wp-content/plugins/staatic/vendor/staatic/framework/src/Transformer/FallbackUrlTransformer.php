<?php

namespace Staatic\Framework\Transformer;

use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\FallbackUrlExtractor;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
final class FallbackUrlTransformer implements TransformerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var FallbackUrlExtractor
     */
    private $extractor;
    public function __construct(UrlTransformerInterface $urlTransformer, ?string $filterBasePath = null, bool $extendedUrlContext = \false)
    {
        $this->logger = new NullLogger();
        $this->extractor = new FallbackUrlExtractor($filterBasePath, null, function (UriInterface $url, ?UriInterface $foundOnUrl, array $context) use ($urlTransformer) {
            return $urlTransformer->transform($url, $foundOnUrl, $context);
        }, $extendedUrlContext);
    }
    /**
     * @param Result $result
     */
    public function supports($result): bool
    {
        if (!$result->size()) {
            return \false;
        }
        if (!$result->originalUrl()) {
            return \false;
        }
        $supportedMimeTypes = array_merge(ResponseUtil::JAVASCRIPT_MIME_TYPES, ResponseUtil::XML_MIME_TIMES, ['text/css', 'text/html']);
        return in_array($result->mimeType(), $supportedMimeTypes);
    }
    /**
     * @param Result $result
     * @param Resource $resource
     */
    public function transform($result, $resource): void
    {
        $this->logger->info("Applying unmatched url transformation on '{$result->url()}'");
        $generator = $this->extractor->extract((string) $resource->content(), $result->originalUrl());
        $numReplacements = $this->applyGenerator($generator);
        $this->logger->debug("Applied {$numReplacements} unmatched url replacements");
        $resource->replace(Utils::streamFor($generator->getReturn()));
        $result->syncResource($resource);
    }
    private function applyGenerator(Generator $generator): int
    {
        $numReplacements = 0;
        while ((bool) $generator->current()) {
            $numReplacements++;
            $generator->next();
        }
        return $numReplacements;
    }
}
