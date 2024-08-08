<?php

namespace Staatic\Crawler;

final class PendingCrawl
{
    /**
     * @var CrawlUrl
     */
    private $crawlUrl;
    /**
     * @var float
     */
    private $startTime;
    /**
     * @var float|null
     */
    private $endTime;
    public function __construct(CrawlUrl $crawlUrl, float $startTime, ?float $endTime = null)
    {
        $this->crawlUrl = $crawlUrl;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
    public static function create(CrawlUrl $crawlUrl): self
    {
        return new static($crawlUrl, microtime(\true));
    }
    public function crawlUrl(): CrawlUrl
    {
        return $this->crawlUrl;
    }
    public function startTime(): float
    {
        return $this->startTime;
    }
    public function endTime(): float
    {
        return $this->endTime;
    }
    public function timeTaken(): float
    {
        return $this->endTime - $this->startTime;
    }
    public function withEndTime(): self
    {
        $newPendingCrawl = clone $this;
        $newPendingCrawl->endTime = microtime(\true);
        return $newPendingCrawl;
    }
}
