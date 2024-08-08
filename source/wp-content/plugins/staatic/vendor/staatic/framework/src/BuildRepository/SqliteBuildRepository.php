<?php

namespace Staatic\Framework\BuildRepository;

use DateTimeImmutable;
use Exception;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use SQLite3;
use SQLite3Stmt;
use Staatic\Framework\Build;
final class SqliteBuildRepository implements BuildRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            id TEXT NOT NULL,
            entry_url TEXT NOT NULL,
            destination_url TEXT NOT NULL,
            parent_id TEXT,
            date_created TEXT NOT NULL,
            date_crawl_started TEXT,
            date_crawl_finished TEXT,
            num_urls_crawlable INTEGER,
            num_urls_crawled INTEGER,
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
    public function __construct(string $databasePath, string $tableName = 'staatic_builds')
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
            throw new RuntimeException("Unable to create build repository table: {$e->getMessage()}");
        }
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
        $this->logger->debug("Adding build #{$build->id()}", ['buildId' => $build->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->tableName} (\n                    id, entry_url, destination_url, parent_id, date_created,\n                    date_crawl_started, date_crawl_finished, num_urls_crawlable, num_urls_crawled\n                ) VALUES (\n                    :id, :entryUrl, :destinationUrl, :parentId, :dateCreated,\n                    :dateCrawlStarted, :dateCrawlFinished, :numUrlsCrawlable, :numUrlsCrawled\n                )\n            ");
            $this->bindBuildValues($build, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to add build #{$build->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param Build $build
     */
    public function update($build): void
    {
        $this->logger->debug("Updating build #{$build->id()}", ['buildId' => $build->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                UPDATE {$this->tableName}\n                SET entry_url = :entryUrl,\n                    destination_url = :destinationUrl,\n                    parent_id = :parentId,\n                    date_created = :dateCreated,\n                    date_crawl_started = :dateCrawlStarted,\n                    date_crawl_finished = :dateCrawlFinished,\n                    num_urls_crawlable = :numUrlsCrawlable,\n                    num_urls_crawled = :numUrlsCrawled\n                WHERE id = :id\n            ");
            $this->bindBuildValues($build, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to update build #{$build->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param string $buildId
     */
    public function find($buildId): ?Build
    {
        try {
            $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
            $statement->bindValue(':id', $buildId, \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to find build #{$buildId}: {$e->getMessage()}");
        }
        $row = $result->fetchArray(\SQLITE3_ASSOC);
        return is_array($row) ? $this->rowToBuild($row) : null;
    }
    private function bindBuildValues(Build $build, SQLite3Stmt $statement): void
    {
        $statement->bindValue(':id', $build->id(), \SQLITE3_TEXT);
        $statement->bindValue(':entryUrl', $build->entryUrl(), \SQLITE3_TEXT);
        $statement->bindValue(':destinationUrl', $build->destinationUrl(), \SQLITE3_TEXT);
        $statement->bindValue(':parentId', $build->parentId(), \SQLITE3_TEXT);
        $statement->bindValue(':dateCreated', $build->dateCreated()->format('c'), \SQLITE3_TEXT);
        $statement->bindValue(':dateCrawlStarted', $build->dateCrawlStarted() ? $build->dateCrawlStarted()->format('c') : null, \SQLITE3_TEXT);
        $statement->bindValue(':dateCrawlFinished', $build->dateCrawlFinished() ? $build->dateCrawlFinished()->format('c') : null, \SQLITE3_TEXT);
        $statement->bindValue(':numUrlsCrawlable', $build->numUrlsCrawlable(), \SQLITE3_NUM);
        $statement->bindValue(':numUrlsCrawled', $build->numUrlsCrawled(), \SQLITE3_NUM);
    }
    private function rowToBuild(array $row): Build
    {
        return new Build($row['id'], new Uri($row['entry_url']), new Uri($row['destination_url']), $row['parent_id'] ? $row['parent_id'] : null, new DateTimeImmutable($row['date_created']), $row['date_crawl_started'] ? new DateTimeImmutable($row['date_crawl_started']) : null, $row['date_crawl_finished'] ? new DateTimeImmutable($row['date_crawl_finished']) : null, (int) $row['num_urls_crawlable'], (int) $row['num_urls_crawled']);
    }
}
