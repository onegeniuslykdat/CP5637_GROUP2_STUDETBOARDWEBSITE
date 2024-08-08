<?php

namespace Staatic\Crawler\Observer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use SplObserver;
use SplSubject;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\Event\StartsCrawling;
use Staatic\Crawler\Event\CrawlRequestFulfilled;
use Staatic\Crawler\Event\CrawlRequestRejected;
use Staatic\Crawler\Event\FinishedCrawling;
use Throwable;
abstract class AbstractObserver implements SplObserver
{
    public function update(SplSubject $crawler): void
    {
        if (!$crawler instanceof CrawlerInterface) {
            return;
        }
        $event = $crawler->getEvent();
        if ($event instanceof StartsCrawling) {
            $this->startsCrawling();
        } elseif ($event instanceof CrawlRequestFulfilled) {
            $this->crawlFulfilled($event->url(), $event->transformedUrl(), $event->normalizedUrl(), $event->response(), $event->foundOnUrl(), $event->tags());
        } elseif ($event instanceof CrawlRequestRejected) {
            $this->crawlRejected($event->url(), $event->transformedUrl(), $event->normalizedUrl(), $event->transferException(), $event->foundOnUrl(), $event->tags());
        } elseif ($event instanceof FinishedCrawling) {
            $this->finishedCrawling();
        }
    }
    public function startsCrawling(): void
    {
    }
    /**
     * @param UriInterface $url
     * @param UriInterface $transformedUrl
     * @param UriInterface $normalizedUrl
     * @param ResponseInterface $response
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $tags
     */
    abstract public function crawlFulfilled($url, $transformedUrl, $normalizedUrl, $response, $foundOnUrl, $tags): void;
    /**
     * @param UriInterface $url
     * @param UriInterface $transformedUrl
     * @param UriInterface $normalizedUrl
     * @param Throwable $transferException
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $tags
     */
    abstract public function crawlRejected($url, $transformedUrl, $normalizedUrl, $transferException, $foundOnUrl, $tags): void;
    public function finishedCrawling(): void
    {
    }
}
