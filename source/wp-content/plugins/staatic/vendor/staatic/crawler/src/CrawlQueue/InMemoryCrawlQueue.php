<?php

namespace Staatic\Crawler\CrawlQueue;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use SplPriorityQueue;
use Staatic\Crawler\CrawlUrl;
class InMemoryCrawlQueue implements CrawlQueueInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var SplPriorityQueue
     */
    private $decoratedQueue;
    public function __construct()
    {
        $this->logger = new NullLogger();
        $this->decoratedQueue = new SplPriorityQueue();
    }
    public function clear(): void
    {
        $this->logger->debug('Clearing crawl queue');
        $this->decoratedQueue = new SplPriorityQueue();
    }
    /**
     * @param CrawlUrl $crawlUrl
     * @param int $priority
     */
    public function enqueue($crawlUrl, $priority): void
    {
        $this->logger->debug("Enqueueing crawl url '{$crawlUrl->url()}' (priority {$priority})", ['crawlUrlId' => $crawlUrl->id()]);
        $this->decoratedQueue->insert($crawlUrl, $priority);
    }
    public function dequeue(): CrawlUrl
    {
        if (!$this->decoratedQueue->valid()) {
            throw new RuntimeException('Unable to dequeue; queue was empty');
        }
        $crawlUrl = $this->decoratedQueue->extract();
        $this->logger->debug("Dequeued crawl url '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
        return $crawlUrl;
    }
    public function count(): int
    {
        return $this->decoratedQueue->count();
    }
}
