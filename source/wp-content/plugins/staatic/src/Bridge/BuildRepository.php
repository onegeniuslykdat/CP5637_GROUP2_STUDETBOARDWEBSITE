<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use DateTimeImmutable;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use Staatic\Framework\Build;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use wpdb;

final class BuildRepository implements BuildRepositoryInterface, LoggerAwareInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var ResultRepository
     */
    private $resultRepository;

    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(wpdb $wpdb, ResultRepository $resultRepository, string $tableName = 'staatic_builds')
    {
        $this->wpdb = $wpdb;
        $this->resultRepository = $resultRepository;
        $this->logger = new NullLogger();
        $this->tableName = $wpdb->prefix . $tableName;
    }

    public function nextId(): string
    {
        return (string) Uuid::uuid4();
    }

    /**
     * @param Build $build
     */
    public function add($build): void
    {
        $this->logger->debug("Adding build {$build->id()}", [
            'buildId' => $build->id()
        ]);
        $result = $this->wpdb->insert($this->tableName, $this->getBuildValues($build));
        if ($result === \false) {
            throw new RuntimeException("Unable to add build #{$build->id()}: {$this->wpdb->last_error}");
        }
    }

    /**
     * @param Build $build
     */
    public function update($build): void
    {
        $this->logger->debug("Updating build #{$build->id()}", [
            'buildId' => $build->id()
        ]);
        $result = $this->wpdb->update($this->tableName, $this->getBuildValues($build), [
            'uuid' => $build->id()
        ]);
        if ($result === \false) {
            throw new RuntimeException("Unable to update build #{$build->id()}: {$this->wpdb->last_error}");
        }
    }

    private function getBuildValues(Build $build): array
    {
        return [
            'uuid' => $build->id(),
            'entry_url' => (string) $build->entryUrl(),
            'destination_url' => (string) $build->destinationUrl(),
            'parent_uuid' => $build->parentId(),
            'date_created' => $build->dateCreated()->format('Y-m-d H:i:s'),
            'date_crawl_started' => $build->dateCrawlStarted() ? $build->dateCrawlStarted()->format(
                'Y-m-d H:i:s'
            ) : null,
            'date_crawl_finished' => $build->dateCrawlFinished() ? $build->dateCrawlFinished()->format(
                'Y-m-d H:i:s'
            ) : null,
            'num_urls_crawlable' => $build->numUrlsCrawlable(),
            'num_urls_crawled' => $build->numUrlsCrawled()
        ];
    }

    /**
     * @param string $buildId
     */
    public function find($buildId): ?Build
    {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->tableName} WHERE uuid = UNHEX(REPLACE(%s, '-', ''))", $buildId),
            \ARRAY_A
        );

        return is_array($row) ? $this->rowToBuild($row) : null;
    }

    private function rowToBuild(array $row): Build
    {
        return new Build((string) Uuid::fromBytes($row['uuid']), new Uri($row['entry_url']), new Uri(
            $row['destination_url']
        ), $row['parent_uuid'] ? (string) Uuid::fromBytes(
            $row['parent_uuid']
        ) : null, new DateTimeImmutable(
            $row['date_created']
        ), $row['date_crawl_started'] ? new DateTimeImmutable(
            $row['date_crawl_started']
        ) : null, $row['date_crawl_finished'] ? new DateTimeImmutable(
            $row['date_crawl_finished']
        ) : null, (int) $row['num_urls_crawlable'], (int) $row['num_urls_crawled']);
    }

    // Plugin specific methods
    /**
     * @param Build $build
     */
    public function delete($build): void
    {
        $this->logger->debug("Deleting build #{$build->id()}", [
            'buildId' => $build->id()
        ]);
        $this->resultRepository->deleteByBuildId($build->id());
        $result = $this->wpdb->delete($this->tableName, [
            'uuid' => $build->id()
        ]);
        if ($result === \false) {
            throw new RuntimeException("Unable to delete build #{$build->id()}: {$this->wpdb->last_error}");
        }
    }

    /**
     * @return Build[]
     */
    public function findAll(): array
    {
        $results = $this->wpdb->get_results("SELECT * FROM {$this->tableName}", \ARRAY_A);

        return array_map(function ($row) {
            return $this->rowToBuild($row);
        }, $results);
    }

    /**
     * @return Build[]
     * @param mixed[] $buildIds
     */
    public function findByIds($buildIds): array
    {
        $buildIds = array_map(function ($buildId) {
            return sprintf("UNHEX(REPLACE('%s', '-', ''))", esc_sql($buildId));
        }, $buildIds);
        $results = $this->wpdb->get_results(
            sprintf("SELECT * FROM {$this->tableName} WHERE uuid IN (%s)", implode(', ', $buildIds)),
            \ARRAY_A
        );

        return array_map(function ($row) {
            return $this->rowToBuild($row);
        }, $results);
    }

    /**
     * @return Build[]
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function findWhereMatching($query, $limit, $offset, $orderBy, $direction): array
    {
        $orderBy = $orderBy ?: 'date_created';
        $direction = $direction ?: 'DESC';
        $results = $this->wpdb->get_results(
            "\n            SELECT *\n            FROM {$this->tableName}\n            WHERE TRUE" . ($query ? "\n                AND entry_url LIKE '%" . esc_sql(
                $this->wpdb->esc_like($query)
            ) . '%\'' : '') . "\n            ORDER BY {$orderBy} {$direction}\n            LIMIT {$limit} OFFSET {$offset}",
            \ARRAY_A
        );

        return array_map(function ($row) {
            return $this->rowToBuild($row);
        }, $results);
    }

    /**
     * @param string|null $query
     */
    public function countWhereMatching($query): int
    {
        return (int) $this->wpdb->get_var(
            "\n            SELECT COUNT(*)\n            FROM {$this->tableName}\n            WHERE TRUE" . ($query ? "\n            AND entry_url LIKE '%" . esc_sql(
                $this->wpdb->esc_like($query)
            ) . '%\'' : '')
        );
    }
}
