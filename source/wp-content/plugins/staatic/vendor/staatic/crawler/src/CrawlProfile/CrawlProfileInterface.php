<?php

namespace Staatic\Crawler\CrawlProfile;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
interface CrawlProfileInterface
{
    public function baseUrl(): UriInterface;
    public function destinationUrl(): UriInterface;
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool;
    /**
     * @param UriInterface $resolvedUrl
     */
    public function normalizeUrl($resolvedUrl): UriInterface;
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transformUrl($url, $foundOnUrl = null, $context = []): UrlTransformation;
}
