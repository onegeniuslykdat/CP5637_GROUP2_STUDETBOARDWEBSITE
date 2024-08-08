<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
class RedirectResponseHandler extends AbstractResponseHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl): CrawlUrl
    {
        if ($crawlUrl->response() && ResponseUtil::isRedirectResponse($crawlUrl->response())) {
            return $this->handleRedirectResponse($crawlUrl);
        }
        return parent::handle($crawlUrl);
    }
    private function handleRedirectResponse(CrawlUrl $crawlUrl): CrawlUrl
    {
        $redirectUrl = ResponseUtil::getRedirectUrl($crawlUrl->response());
        if (!$redirectUrl) {
            return $crawlUrl;
        }
        $resolvedUrl = UriResolver::resolve($crawlUrl->url(), $redirectUrl);
        if (!$this->crawler->shouldCrawl($resolvedUrl)) {
            return $crawlUrl;
        }
        $urlTransformation = $this->crawler->transformUrl($resolvedUrl, $crawlUrl->url());
        $crawlUrl = $crawlUrl->withResponse($crawlUrl->response()->withHeader('Location', (string) $urlTransformation->effectiveUrl()));
        if ($crawlUrl->hasTag(CrawlerInterface::TAG_DONT_FOLLOW)) {
            return $crawlUrl;
        }
        if ($this->hasExceededMaxRedirects($crawlUrl)) {
            return $crawlUrl;
        }
        $this->crawler->addToCrawlQueue(CrawlUrl::create($resolvedUrl, $crawlUrl, \true, $crawlUrl->tags(), $urlTransformation->transformedUrl()));
        return $crawlUrl;
    }
    private function hasExceededMaxRedirects(CrawlUrl $crawlUrl): bool
    {
        $maxRedirects = $this->crawler->crawlOptions()->maxRedirects();
        return $crawlUrl->redirectLevel() >= $maxRedirects;
    }
}
