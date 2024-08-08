<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
class EntryCrawlUrlProvider implements CrawlUrlProviderInterface
{
    /**
     * @var UriInterface|string
     */
    private $url;
    /**
     * @param UriInterface|string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }
    public function provide(): Generator
    {
        yield CrawlUrl::create(is_string($this->url) ? new Uri($this->url) : $this->url, null, false, [CrawlerInterface::TAG_ENTRY_URL]);
    }
}
