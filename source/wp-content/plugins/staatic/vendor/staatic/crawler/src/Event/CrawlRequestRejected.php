<?php

namespace Staatic\Crawler\Event;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlUrl;
use Throwable;
class CrawlRequestRejected implements EventInterface
{
    /**
     * @var UriInterface
     */
    private $url;
    /**
     * @var UriInterface
     */
    private $transformedUrl;
    /**
     * @var UriInterface
     */
    private $normalizedUrl;
    /**
     * @var Throwable
     */
    private $transferException;
    /**
     * @var UriInterface|null
     */
    private $foundOnUrl;
    /**
     * @var mixed[]
     */
    private $tags = [];
    public function __construct(UriInterface $url, UriInterface $transformedUrl, UriInterface $normalizedUrl, Throwable $transferException, ?UriInterface $foundOnUrl = null, array $tags = [])
    {
        $this->url = $url;
        $this->transformedUrl = $transformedUrl;
        $this->normalizedUrl = $normalizedUrl;
        $this->transferException = $transferException;
        $this->foundOnUrl = $foundOnUrl;
        $this->tags = $tags;
    }
    /**
     * @param CrawlUrl $crawlUrl
     * @param Throwable $transferException
     */
    public static function create($crawlUrl, $transferException): self
    {
        return new self($crawlUrl->url(), $crawlUrl->transformedUrl(), $crawlUrl->normalizedUrl(), $transferException, $crawlUrl->foundOnUrl(), $crawlUrl->tags());
    }
    public function url(): UriInterface
    {
        return $this->url;
    }
    public function transformedUrl(): UriInterface
    {
        return $this->transformedUrl;
    }
    public function normalizedUrl(): UriInterface
    {
        return $this->normalizedUrl;
    }
    public function transferException(): Throwable
    {
        return $this->transferException;
    }
    public function foundOnUrl(): ?UriInterface
    {
        return $this->foundOnUrl;
    }
    public function tags(): array
    {
        return $this->tags;
    }
}
