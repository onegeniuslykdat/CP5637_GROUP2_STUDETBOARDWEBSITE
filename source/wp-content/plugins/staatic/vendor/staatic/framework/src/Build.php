<?php

namespace Staatic\Framework;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class Build
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var UriInterface
     */
    private $entryUrl;
    /**
     * @var UriInterface
     */
    private $destinationUrl;
    /**
     * @var string|null
     */
    private $parentId;
    /**
     * @var DateTimeInterface|null
     */
    private $dateCrawlStarted;
    /**
     * @var DateTimeInterface|null
     */
    private $dateCrawlFinished;
    /**
     * @var int
     */
    private $numUrlsCrawlable = 0;
    /**
     * @var int
     */
    private $numUrlsCrawled = 0;
    /**
     * @var DateTimeInterface
     */
    private $dateCreated;
    public function __construct(string $id, UriInterface $entryUrl, UriInterface $destinationUrl, ?string $parentId = null, ?DateTimeInterface $dateCreated = null, ?DateTimeInterface $dateCrawlStarted = null, ?DateTimeInterface $dateCrawlFinished = null, int $numUrlsCrawlable = 0, int $numUrlsCrawled = 0)
    {
        $this->id = $id;
        $this->entryUrl = $entryUrl;
        $this->destinationUrl = $destinationUrl;
        $this->parentId = $parentId;
        $this->dateCrawlStarted = $dateCrawlStarted;
        $this->dateCrawlFinished = $dateCrawlFinished;
        $this->numUrlsCrawlable = $numUrlsCrawlable;
        $this->numUrlsCrawled = $numUrlsCrawled;
        $this->dateCreated = $dateCreated ?: new DateTimeImmutable();
    }
    public function __toString()
    {
        return (string) $this->id;
    }
    public function id(): string
    {
        return $this->id;
    }
    public function parentId(): ?string
    {
        return $this->parentId;
    }
    public function entryUrl(): UriInterface
    {
        return $this->entryUrl;
    }
    public function destinationUrl(): UriInterface
    {
        return $this->destinationUrl;
    }
    public function dateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }
    public function dateCrawlStarted(): ?DateTimeInterface
    {
        return $this->dateCrawlStarted;
    }
    public function dateCrawlFinished(): ?DateTimeInterface
    {
        return $this->dateCrawlFinished;
    }
    public function isFinishedCrawling(): bool
    {
        return (bool) $this->dateCrawlFinished;
    }
    public function numUrlsCrawlable(): int
    {
        return $this->numUrlsCrawlable;
    }
    public function numUrlsCrawled(): int
    {
        return $this->numUrlsCrawled;
    }
    public function crawlStarted(): void
    {
        $this->dateCrawlStarted = new DateTimeImmutable();
    }
    public function crawlFinished(): void
    {
        $this->dateCrawlFinished = new DateTimeImmutable();
    }
    /**
     * @param int $numUrlsCrawlable
     */
    public function queuedUrls($numUrlsCrawlable): void
    {
        $this->numUrlsCrawlable = $numUrlsCrawlable;
    }
    /**
     * @param int $numUrlsCrawlable
     */
    public function crawledUrl($numUrlsCrawlable): void
    {
        $this->numUrlsCrawlable = $numUrlsCrawlable;
        $this->numUrlsCrawled++;
    }
    /**
     * @param int $numUrlsCrawlable
     * @param int $numCrawled
     */
    public function crawledUrls($numUrlsCrawlable, $numCrawled): void
    {
        $this->numUrlsCrawlable = $numUrlsCrawlable;
        $this->numUrlsCrawled = $numCrawled;
    }
}
