<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
abstract class AbstractResponseHandler implements ResponseHandlerInterface
{
    /**
     * @var CrawlerInterface
     */
    protected $crawler;
    /**
     * @var ResponseHandlerInterface|null
     */
    private $nextHandler;
    /**
     * @param CrawlerInterface $crawler
     */
    public function setCrawler($crawler)
    {
        $this->crawler = $crawler;
    }
    /**
     * @param ResponseHandlerInterface $nextHandler
     */
    public function setNext($nextHandler): ResponseHandlerInterface
    {
        $this->nextHandler = $nextHandler;
        return $nextHandler;
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl): CrawlUrl
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($crawlUrl);
        }
        return $crawlUrl;
    }
    protected function urlFilterCallback(): callable
    {
        return function (UriInterface $resolvedUrl, array $context) {
            return !$this->crawler->shouldCrawl($resolvedUrl, $context);
        };
    }
    protected function urlTransformCallback(): callable
    {
        return function (UriInterface $url, ?UriInterface $foundOnUrl, array $context) {
            return $this->crawler->transformUrl($url, $foundOnUrl, $context);
        };
    }
    /**
     * @param CrawlUrl $crawlUrl
     * @param iterable $extractedUrls
     * @param bool $copyTags
     */
    protected function processExtractedUrls($crawlUrl, $extractedUrls, $copyTags = \false): void
    {
        $dontFollow = $crawlUrl->hasTag(CrawlerInterface::TAG_DONT_FOLLOW);
        foreach ($extractedUrls as $resolvedUrl => $transformedUrl) {
            if ($dontFollow) {
                continue;
            }
            $this->crawler->addToCrawlQueue(CrawlUrl::create(new Uri($resolvedUrl), $crawlUrl, false, $copyTags ? $crawlUrl->tags() : [], $transformedUrl));
        }
    }
}
