<?php

namespace Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
class AdditionalUrl
{
    /**
     * @var string
     */
    private $priority = self::PRIORITY_NORMAL;
    /**
     * @var bool
     */
    private $dontTouch = \false;
    /**
     * @var bool
     */
    private $dontFollow = \false;
    /**
     * @var bool
     */
    private $dontSave = \false;
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_LOW = 'low';
    /**
     * @var UriInterface
     */
    private $url;
    /**
     * @param UriInterface|string $url
     */
    public function __construct($url, string $priority = self::PRIORITY_NORMAL, bool $dontTouch = \false, bool $dontFollow = \false, bool $dontSave = \false)
    {
        $this->priority = $priority;
        $this->dontTouch = $dontTouch;
        $this->dontFollow = $dontFollow;
        $this->dontSave = $dontSave;
        $this->url = is_string($url) ? new Uri($url) : $url;
    }
    /**
     * @param UriInterface|null $baseUrl
     */
    public function createCrawlUrl($baseUrl): CrawlUrl
    {
        return CrawlUrl::create($baseUrl ? UriResolver::resolve($baseUrl, $this->url) : $this->url, null, false, $this->tags());
    }
    public function url(): UriInterface
    {
        return $this->url;
    }
    public function priority(): string
    {
        return $this->priority;
    }
    public function dontTouch(): bool
    {
        return $this->dontTouch;
    }
    public function dontFollow(): bool
    {
        return $this->dontFollow;
    }
    public function dontSave(): bool
    {
        return $this->dontSave;
    }
    private function tags(): array
    {
        $tags = [];
        if ($this->priority === self::PRIORITY_HIGH) {
            $tags[] = CrawlerInterface::TAG_PRIORITY_HIGH;
        } elseif ($this->priority === self::PRIORITY_LOW) {
            $tags[] = CrawlerInterface::TAG_PRIORITY_LOW;
        }
        if ($this->dontTouch) {
            $tags[] = CrawlerInterface::TAG_DONT_TOUCH;
        }
        if ($this->dontFollow) {
            $tags[] = CrawlerInterface::TAG_DONT_FOLLOW;
        }
        if ($this->dontSave) {
            $tags[] = CrawlerInterface::TAG_DONT_SAVE;
        }
        return $tags;
    }
}
