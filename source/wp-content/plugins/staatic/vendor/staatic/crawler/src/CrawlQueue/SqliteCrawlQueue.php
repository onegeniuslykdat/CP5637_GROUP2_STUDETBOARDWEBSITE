<?php

namespace Staatic\Crawler\CrawlQueue;

use Exception;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use SQLite3;
use Staatic\Crawler\CrawlUrl;
final class SqliteCrawlQueue implements CrawlQueueInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            id TEXT NOT NULL,
            url TEXT NOT NULL,
            origin_url TEXT NOT NULL,
            transformed_url TEXT NOT NULL,
            normalized_url TEXT NOT NULL,
            found_on_url TEXT,
            depth_level INTEGER NOT NULL,
            redirect_level INTEGER NOT NULL,
            tags TEXT NOT NULL,
            priority INTEGER NOT NULL,
            position INTEGER NOT NULL,
            PRIMARY KEY (id)
        )';
    /**
     * @var SQLite3
     */
    private $sqlite;
    /**
     * @var string
     */
    private $tableName;
    public function __construct(string $databasePath, string $tableName = 'staatic_crawl_queue')
    {
        $this->logger = new NullLogger();
        $this->sqlite = new SQLite3($databasePath);
        $this->sqlite->enableExceptions(\true);
        $this->tableName = $tableName;
    }
    public function __destruct()
    {
        $this->sqlite->close();
    }
    public function createTable()
    {
        try {
            $this->sqlite->exec(sprintf(self::TABLE_DEFINITION, $this->tableName));
        } catch (Exception $e) {
            throw new RuntimeException("Unable to create crawl queue table: {$e->getMessage()}", 0, $e);
        }
    }
    public function clear(): void
    {
        $this->logger->debug('Clearing crawl queue');
        try {
            $this->sqlite->exec("DELETE FROM {$this->tableName}");
        } catch (Exception $e) {
            throw new RuntimeException("Unable to clear crawl queue: {$e->getMessage()}", 0, $e);
        }
    }
    /**
     * @param CrawlUrl $crawlUrl
     * @param int $priority
     */
    public function enqueue($crawlUrl, $priority): void
    {
        $this->logger->debug("Enqueueing crawl url '{$crawlUrl->url()}' (priority {$priority})", ['crawlUrlId' => $crawlUrl->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->tableName} (\n                    id, url, origin_url, transformed_url, normalized_url, found_on_url,\n                    depth_level, redirect_level, tags, priority,\n                    position\n                ) VALUES (\n                    :id, :url, :originUrl, :transformedUrl, :normalizedUrl, :foundOnUrl,\n                    :depthLevel, :redirectLevel, :tags, :priority,\n                    (SELECT IFNULL(MAX(position), 0) + 1 FROM {$this->tableName})\n                )\n            ");
            $statement->bindValue(':id', $crawlUrl->id(), \SQLITE3_TEXT);
            $statement->bindValue(':url', $crawlUrl->url(), \SQLITE3_TEXT);
            $statement->bindValue(':originUrl', $crawlUrl->originUrl(), \SQLITE3_TEXT);
            $statement->bindValue(':transformedUrl', $crawlUrl->transformedUrl(), \SQLITE3_TEXT);
            $statement->bindValue(':normalizedUrl', $crawlUrl->normalizedUrl(), \SQLITE3_TEXT);
            $statement->bindValue(':foundOnUrl', $crawlUrl->foundOnUrl() ? $crawlUrl->foundOnUrl() : null, \SQLITE3_TEXT);
            $statement->bindValue(':depthLevel', $crawlUrl->depthLevel(), \SQLITE3_INTEGER);
            $statement->bindValue(':redirectLevel', $crawlUrl->redirectLevel(), \SQLITE3_INTEGER);
            $statement->bindValue(':tags', implode(',', $crawlUrl->tags()), \SQLITE3_TEXT);
            $statement->bindValue(':priority', $priority, \SQLITE3_INTEGER);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to enqueue crawl url '{$crawlUrl->url()}': {$e->getMessage()}", 0, $e);
        }
    }
    public function dequeue(): CrawlUrl
    {
        try {
            $statement = $this->sqlite->prepare("\n                SELECT *\n                FROM {$this->tableName}\n                ORDER BY priority DESC, position ASC\n                LIMIT 1\n            ");
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to dequeue crawl url: {$e->getMessage()}", 0, $e);
        }
        $row = $result->fetchArray(\SQLITE3_ASSOC);
        if ($row === \false) {
            throw new RuntimeException('Unable to dequeue; queue was empty');
        }
        $crawlUrl = $this->rowToCrawlUrl($row);
        try {
            $statement = $this->sqlite->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
            $statement->bindValue(':id', $crawlUrl->id(), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to dequeue crawl url '{$crawlUrl->url()}': {$e->getMessage()}", 0, $e);
        }
        $this->logger->debug("Dequeued crawl url '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
        return $crawlUrl;
    }
    private function rowToCrawlUrl(array $row): CrawlUrl
    {
        return new CrawlUrl($row['id'], new Uri($row['url']), new Uri($row['origin_url']), $row['found_on_url'] ? new Uri($row['found_on_url']) : null, $row['depth_level'], $row['redirect_level'], $row['tags'] ? explode(',', $row['tags']) : [], new Uri($row['transformed_url']), new Uri($row['normalized_url']));
    }
    public function count(): int
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName}");
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count crawl queue: {$e->getMessage()}", 0, $e);
        }
        $row = $result->fetchArray(\SQLITE3_NUM);
        return (int) $row[0];
    }
}
