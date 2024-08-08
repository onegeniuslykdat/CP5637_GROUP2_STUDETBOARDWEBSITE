<?php

namespace Staatic\Framework\DeploymentRepository;

use DateTimeImmutable;
use Exception;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use SQLite3;
use SQLite3Stmt;
use Staatic\Framework\Deployment;
final class SqliteDeploymentRepository implements DeploymentRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            id TEXT NOT NULL,
            build_id TEXT NOT NULL,
            date_created TEXT NOT NULL,
            date_started TEXT,
            date_finished TEXT,
            num_results_total INTEGER,
            num_results_deployable INTEGER,
            num_results_deployed INTEGER,
            metadata TEXT,
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
    public function __construct(string $databasePath, string $tableName = 'staatic_deployments')
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
            throw new RuntimeException("Unable to create deployment repositoy table: {$e->getMessage()}");
        }
    }
    public function nextId(): string
    {
        return (string) Uuid::uuid4();
    }
    /**
     * @param Deployment $deployment
     */
    public function add($deployment): void
    {
        $this->logger->debug("Adding deployment #{$deployment->id()}", ['deploymentId' => $deployment->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->tableName} (\n                    id, build_id, date_created,\n                    date_started, date_finished,\n                    num_results_total, num_results_deployable, num_results_deployed,\n                    metadata\n                ) VALUES (\n                    :id, :buildId, :dateCreated,\n                    :dateStarted, :dateFinished,\n                    :numResultsTotal, :numResultsDeployable, :numResultsDeployed,\n                    :metadata\n                )\n            ");
            $this->bindDeploymentValues($deployment, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to add deployment #{$deployment->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param Deployment $deployment
     */
    public function update($deployment): void
    {
        $this->logger->debug("Updating deployment #{$deployment->id()}", ['deploymentId' => $deployment->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                UPDATE {$this->tableName}\n                SET build_id = :buildId,\n                    date_created = :dateCreated,\n                    date_started = :dateStarted,\n                    date_finished = :dateFinished,\n                    num_results_total = :numResultsTotal,\n                    num_results_deployable = :numResultsDeployable,\n                    num_results_deployed = :numResultsDeployed,\n                    metadata = :metadata\n                WHERE id = :id\n            ");
            $this->bindDeploymentValues($deployment, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to update deployment #{$deployment->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param string $deploymentId
     */
    public function find($deploymentId): ?Deployment
    {
        try {
            $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
            $statement->bindValue(':id', $deploymentId, \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to find deployment #{$deploymentId}: {$e->getMessage()}");
        }
        $row = $result->fetchArray(\SQLITE3_ASSOC);
        return is_array($row) ? $this->rowToDeployment($row) : null;
    }
    private function bindDeploymentValues(Deployment $deployment, SQLite3Stmt $statement): void
    {
        $statement->bindValue(':id', $deployment->id(), \SQLITE3_TEXT);
        $statement->bindValue(':buildId', $deployment->buildId(), \SQLITE3_TEXT);
        $statement->bindValue(':dateCreated', $deployment->dateCreated()->format('c'), \SQLITE3_TEXT);
        $statement->bindValue(':dateStarted', $deployment->dateStarted() ? $deployment->dateStarted()->format('c') : null, \SQLITE3_TEXT);
        $statement->bindValue(':dateFinished', $deployment->dateFinished() ? $deployment->dateFinished()->format('c') : null, \SQLITE3_TEXT);
        $statement->bindValue(':numResultsTotal', $deployment->numResultsTotal(), \SQLITE3_NUM);
        $statement->bindValue(':numResultsDeployable', $deployment->numResultsDeployable(), \SQLITE3_NUM);
        $statement->bindValue(':numResultsDeployed', $deployment->numResultsDeployed(), \SQLITE3_NUM);
        $statement->bindValue(':metadata', $deployment->metadata() ? json_encode($deployment->metadata()) : null, \SQLITE3_TEXT);
    }
    private function rowToDeployment(array $row): Deployment
    {
        return new Deployment($row['id'], $row['build_id'], new DateTimeImmutable($row['date_created']), $row['date_started'] ? new DateTimeImmutable($row['date_started']) : null, $row['date_finished'] ? new DateTimeImmutable($row['date_finished']) : null, (int) $row['num_results_total'], (int) $row['num_results_deployable'], (int) $row['num_results_deployed'], $row['metadata'] ? json_decode($row['metadata'], \true) : null);
    }
}
