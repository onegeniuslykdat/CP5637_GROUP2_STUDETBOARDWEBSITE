<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
class AdditionalUrlCrawlUrlProvider implements CrawlUrlProviderInterface
{
    /**
     * @var iterable
     */
    private $additionalUrls;
    /**
     * @var UriInterface|null
     */
    private $baseUrl;
    /**
     * @param UriInterface|string|null $baseUrl
     */
    public function __construct(iterable $additionalUrls, $baseUrl = null)
    {
        $this->additionalUrls = $additionalUrls;
        $this->baseUrl = is_string($baseUrl) ? new Uri($baseUrl) : $baseUrl;
    }
    public function provide(): Generator
    {
        foreach ($this->additionalUrls as $additionalUrl) {
            yield $additionalUrl->createCrawlUrl($this->baseUrl);
        }
    }
}
