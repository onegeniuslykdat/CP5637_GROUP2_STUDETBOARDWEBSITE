<?php

namespace Staatic\Crawler\KnownUrlsContainer;

use Exception;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use SQLite3;
final class SqliteKnownUrlsContainer implements KnownUrlsContainerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            hash TEXT NOT NULL,
            PRIMARY KEY (hash)
        )';
    /**
     * @var SQLite3
     */
    private $sqlite;
    /**
     * @var string
     */
    private $tableName;
    public function __construct(string $databasePath, string $tableName = 'staatic_known_urls')
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
    public function createTable(): void
    {
        try {
            $this->sqlite->exec(sprintf(self::TABLE_DEFINITION, $this->tableName));
        } catch (Exception $e) {
            throw new RuntimeException("Unable to create known urls table: {$e->getMessage()}", 0, $e);
        }
    }
    public function clear(): void
    {
        $this->logger->debug('Clearing container');
        try {
            $this->sqlite->exec("DELETE FROM {$this->tableName}");
        } catch (Exception $e) {
            throw new RuntimeException("Unable to clear container: {$e->getMessage()}", 0, $e);
        }
    }
    /**
     * @param UriInterface $url
     */
    public function add($url): void
    {
        $this->logger->debug("Adding url '{$url}' to container");
        try {
            $statement = $this->sqlite->prepare("INSERT INTO {$this->tableName} (hash) VALUES (:hash)");
            $statement->bindValue(':hash', md5((string) $url), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to add url '{$url}' to container: {$e->getMessage()}", 0, $e);
        }
    }
    /**
     * @param UriInterface $url
     */
    public function isKnown($url): bool
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName} WHERE hash = :hash");
            $statement->bindValue(':hash', md5((string) $url), \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count container: {$e->getMessage()}", 0, $e);
        }
        $row = $result->fetchArray(\SQLITE3_NUM);
        return (bool) $row[0];
    }
    public function count(): int
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName}");
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count container: {$e->getMessage()}", 0, $e);
        }
        $row = $result->fetchArray(\SQLITE3_NUM);
        return (int) $row[0];
    }
}
