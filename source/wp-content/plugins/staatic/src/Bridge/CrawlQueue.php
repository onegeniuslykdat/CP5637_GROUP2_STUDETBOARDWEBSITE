<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use wpdb;

final class CrawlQueue implements CrawlQueueInterface, LoggerAwareInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    use LoggerAwareTrait;

    /** @var int */
    private const MAX_URL_LENGTH = 2083;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(wpdb $wpdb, string $tableName = 'staatic_crawl_queue')
    {
        $this->wpdb = $wpdb;
        $this->logger = new NullLogger();
        $this->tableName = $wpdb->prefix . $tableName;
    }

    public function clear(): void
    {
        $this->logger->debug('Clearing crawl queue');
        $result = $this->wpdb->query("DELETE FROM {$this->tableName}");
        if ($result === \false) {
            throw new RuntimeException("Unable to clear crawl queue: {$this->wpdb->last_error}");
        }
    }

    /**
     * @param CrawlUrl $crawlUrl
     * @param int $priority
     */
    public function enqueue($crawlUrl, $priority): void
    {
        $this->logger->debug("Enqueueing crawl url '{$crawlUrl->url()} (priority {$priority})", [
            'crawlUrlId' => $crawlUrl->id()
        ]);
        $result = $this->wpdb->insert($this->tableName, array_merge($this->getCrawlUrlValues($crawlUrl), [
            'priority' => $priority
        ]));
        if ($result === \false) {
            throw new RuntimeException("Unable to enqueue crawl url '{$crawlUrl->url()}: {$this->wpdb->last_error}");
        }
    }

    private function getCrawlUrlValues(CrawlUrl $crawlUrl): array
    {
        return [
            'uuid' => $crawlUrl->id(),
            'url' => substr((string) $crawlUrl->url(), 0, self::MAX_URL_LENGTH),
            'origin_url' => substr((string) $crawlUrl->originUrl(), 0, self::MAX_URL_LENGTH),
            'transformed_url' => substr((string) $crawlUrl->transformedUrl(), 0, self::MAX_URL_LENGTH),
            'normalized_url' => substr((string) $crawlUrl->normalizedUrl(), 0, self::MAX_URL_LENGTH),
            'found_on_url' => $crawlUrl->foundOnUrl() ? substr(
                (string) $crawlUrl->foundOnUrl(),
                0,
                self::MAX_URL_LENGTH
            ) : null,
            'depth_level' => $crawlUrl->depthLevel(),
            'redirect_level' => $crawlUrl->redirectLevel(),
            'tags' => implode(',', $crawlUrl->tags())
        ];
    }

    public function dequeue(): CrawlUrl
    {
        $row = $this->wpdb->get_row(
            "SELECT * FROM {$this->tableName} ORDER BY priority DESC, id ASC LIMIT 1",
            \ARRAY_A
        );
        if ($row === null) {
            throw new RuntimeException('Unable to dequeue; queue was empty');
        }
        $crawlUrl = $this->rowToCrawlUrl($row);
        $result = $this->wpdb->delete($this->tableName, [
            'uuid' => $crawlUrl->id()
        ]);
        if ($result === \false) {
            throw new RuntimeException("Unable to dequeue crawl url '{$crawlUrl->url()}: {$this->wpdb->last_error}");
        }
        $this->logger->debug("Dequeued crawl url '{$crawlUrl->url()}'", [
            'crawlUrlId' => $crawlUrl->id()
        ]);

        return $crawlUrl;
    }

    private function rowToCrawlUrl(array $row): CrawlUrl
    {
        return new CrawlUrl((string) Uuid::fromBytes($row['uuid']), new Uri($row['url']), new Uri(
            $row['origin_url']
        ), $row['found_on_url'] ? new Uri(
            $row['found_on_url']
        ) : null, (int) $row['depth_level'], (int) $row['redirect_level'], $row['tags'] ? explode(
            ',',
            $row['tags']
        ) : [], new Uri(
            $row['transformed_url']
        ), new Uri(
            $row['normalized_url']
        ));
    }

    public function count(): int
    {
        return (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->tableName}");
    }
}
