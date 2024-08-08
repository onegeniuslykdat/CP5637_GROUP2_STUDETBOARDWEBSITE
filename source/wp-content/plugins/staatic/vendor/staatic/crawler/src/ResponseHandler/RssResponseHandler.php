<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\UrlExtractorInterface;
use Staatic\Crawler\UrlExtractor\RssUrlExtractor;
class RssResponseHandler extends AbstractResponseHandler implements LoggerAwareInterface
{
    /**
     * @var UrlExtractorInterface|null
     */
    private $extractor;
    use LoggerAwareTrait;
    public function __construct(?UrlExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor;
        $this->logger = new NullLogger();
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl): CrawlUrl
    {
        if ($this->isRssResponse($crawlUrl->response())) {
            $crawlUrl = $this->handleRssResponse($crawlUrl);
        }
        return parent::handle($crawlUrl);
    }
    private function isRssResponse(ResponseInterface $response): bool
    {
        return ResponseUtil::getMimeType($response) === 'application/rss+xml';
    }
    private function handleRssResponse(CrawlUrl $crawlUrl): CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->extractor()->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
    private function extractor(): UrlExtractorInterface
    {
        if (!$this->extractor) {
            $this->extractor = new RssUrlExtractor($this->urlFilterCallback(), $this->urlTransformCallback(), $this->crawler->crawlOptions()->extendedUrlContext());
        }
        return $this->extractor;
    }
}
