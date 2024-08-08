<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use DateTimeImmutable;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Framework\Build;
use Staatic\Framework\Deployment;
use Staatic\WordPress\Bridge\BuildRepository;
use Staatic\WordPress\Bridge\DeploymentRepository;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;
use wpdb;

final class PublicationRepository implements LoggerAwareInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var BuildRepository
     */
    private $buildRepository;

    /**
     * @var DeploymentRepository
     */
    private $deploymentRepository;

    /**
     * @var WorkDirectorySetting
     */
    private $workDirectory;

    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(wpdb $wpdb, BuildRepository $buildRepository, DeploymentRepository $deploymentRepository, WorkDirectorySetting $workDirectory, string $tableName = 'staatic_publications')
    {
        $this->wpdb = $wpdb;
        $this->buildRepository = $buildRepository;
        $this->deploymentRepository = $deploymentRepository;
        $this->workDirectory = $workDirectory;
        $this->logger = new NullLogger();
        $this->tableName = $wpdb->prefix . $tableName;
    }

    public function nextId(): string
    {
        return (string) Uuid::uuid4();
    }

    /**
     * @param Publication $publication
     */
    public function add($publication): void
    {
        $this->logger->debug("Adding publication #{$publication->id()}", [
            'publicationId' => $publication->id()
        ]);
        $this->wpdb->insert($this->tableName, $this->getPublicationValues($publication));
    }

    /**
     * @param Publication $publication
     */
    public function update($publication): void
    {
        $this->logger->debug("Updating publication #{$publication->id()}", [
            'publicationId' => $publication->id()
        ]);
        $this->wpdb->update($this->tableName, $this->getPublicationValues($publication), [
            'uuid' => $publication->id()
        ]);
    }

    /**
     * @param Publication $publication
     */
    public function delete($publication): void
    {
        $this->logger->debug("Deleting publication #{$publication->id()}", [
            'publicationId' => $publication->id()
        ]);
        if ($this->shouldDeleteBuildWithPublication($publication)) {
            $this->buildRepository->delete($publication->build());
        }
        $this->deploymentRepository->delete($publication->deployment());
        $this->wpdb->delete($this->tableName, [
            'uuid' => $publication->id()
        ]);
    }

    private function shouldDeleteBuildWithPublication(Publication $publication): bool
    {
        $query = $this->wpdb->prepare(
            "\n            SELECT COUNT(*)\n            FROM {$this->tableName}\n            WHERE build_uuid = UNHEX(REPLACE(%s, '-', ''))\n                AND uuid != UNHEX(REPLACE(%s, '-', ''))\n        ",
            $publication->build()->id(),
            $publication->id()
        );

        return !$this->wpdb->get_var($query);
    }

    private function getPublicationValues(Publication $publication): array
    {
        return [
            'uuid' => $publication->id(),
            'date_created' => $publication->dateCreated()->format('Y-m-d H:i:s'),
            'build_uuid' => $publication->build()->id(),
            'deployment_uuid' => $publication->deployment()->id(),
            'is_preview' => (int) $publication->isPreview(),
            'user_id' => $publication->userId(),
            'metadata' => json_encode($publication->metadata(), \JSON_UNESCAPED_SLASHES),
            'status' => (string) $publication->status(),
            'date_finished' => ($nullsafeVariable1 = $publication->dateFinished()) ? $nullsafeVariable1->format(
                'Y-m-d H:i:s'
            ) : null,
            'current_task' => $publication->currentTask()
        ];
    }

    /**
     * @param string $publicationId
     */
    public function find($publicationId): ?Publication
    {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->tableName} WHERE uuid = UNHEX(REPLACE(%s, '-', ''))",
                $publicationId
            ),
            \ARRAY_A
        );
        if (!is_array($row)) {
            return null;
        }
        $build = $this->buildRepository->find((string) Uuid::fromBytes($row['build_uuid']));
        $deployment = $this->deploymentRepository->find((string) Uuid::fromBytes($row['deployment_uuid']));

        return $this->rowToPublication($row, $build, $deployment);
    }

    private function rowToPublication(array $row, Build $build, Deployment $deployment): Publication
    {
        return new Publication((string) Uuid::fromBytes($row['uuid']), new DateTimeImmutable(
            $row['date_created']
        ), $build, $deployment, (bool) $row['is_preview'], $row['user_id'] ? (int) $row['user_id'] : null, empty($row['metadata']) ? [] : json_decode(
            $row['metadata'],
            \true
        ), PublicationStatus::create(
            $row['status']
        ), $row['date_finished'] ? new DateTimeImmutable(
            $row['date_finished']
        ) : null, $row['current_task']);
    }

    /**
     * @return Publication[]
     */
    private function rowsToPublication(array $rows): array
    {
        $rows = array_map(function (array $row) {
            $row['build_uuid'] = (string) Uuid::fromBytes($row['build_uuid']);
            $row['deployment_uuid'] = (string) Uuid::fromBytes($row['deployment_uuid']);

            return $row;
        }, $rows);
        $buildIds = array_column($rows, 'build_uuid');
        $builds = $this->buildRepository->findByIds($buildIds);
        $buildsIndexed = [];
        foreach ($builds as $build) {
            $buildsIndexed[$build->id()] = $build;
        }
        $deploymentIds = array_column($rows, 'deployment_uuid');
        $deployments = $this->deploymentRepository->findByIds($deploymentIds);
        $deploymentsIndexed = [];
        foreach ($deployments as $deployment) {
            $deploymentsIndexed[$deployment->id()] = $deployment;
        }

        return array_map(function ($row) use ($buildsIndexed, $deploymentsIndexed) {
            return $this->rowToPublication(
                $row,
                $buildsIndexed[$row['build_uuid']],
                $deploymentsIndexed[$row['deployment_uuid']]
            );
        }, $rows);
    }

    // Plugin specific methods
    /**
     * @return Publication[]
     */
    public function findAll(): array
    {
        $results = $this->wpdb->get_results("SELECT * FROM {$this->tableName}", \ARRAY_A);

        return empty($results) ? [] : $this->rowsToPublication($results);
    }

    private function buildQuery(
        ?string $status,
        ?string $query,
        int $limit = null,
        int $offset = null,
        ?string $orderBy = null,
        ?string $direction = null
    ): string
    {
        if ($orderBy && !in_array($orderBy, ['date_created', 'user_id', 'status'])) {
            throw new InvalidArgumentException("Invalid orderBy column supplied: {$orderBy}");
        }
        if ($direction && !in_array(strtolower($direction), ['asc', 'desc'])) {
            throw new InvalidArgumentException("Invalid direction supplied: {$direction}");
        }

        return "\n            SELECT _.*\n            FROM {$this->tableName} AS _\n                INNER JOIN {$this->wpdb->prefix}staatic_builds AS build ON build.uuid = _.build_uuid\n                INNER JOIN {$this->wpdb->prefix}staatic_deployments AS deployment ON deployment.uuid = _.deployment_uuid\n                LEFT JOIN {$this->wpdb->users} AS user ON user.id = _.user_id\n            WHERE TRUE" . ($query ? "\n                AND user.display_name LIKE '%" . esc_sql(
            $this->wpdb->esc_like($query)
        ) . '%\'' : '') . ($status ? "\n                AND status = '" . esc_sql(
            $status
        ) . '\'' : '') . ($orderBy ? "\n            ORDER BY {$orderBy}" . ($direction ? " {$direction}" : "") : "") . ($limit ? "\n            LIMIT {$limit}" . ($offset ? " OFFSET {$offset}" : "") : "");
    }

    private function transformSelectToCount(string $query): string
    {
        return preg_replace('~^(\s*SELECT\s*).+?(\s*FROM)~', '$1COUNT(*)$2', $query, 1);
    }

    /**
     * @return Publication[]
     * @param string|null $status
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function findWhereMatching($status, $query, $limit, $offset, $orderBy, $direction): array
    {
        $orderBy = $orderBy ?: 'date_created';
        $direction = $direction ?: 'DESC';
        $query = $this->buildQuery($status, $query, $limit, $offset, $orderBy, $direction);
        $results = $this->wpdb->get_results($query, \ARRAY_A);

        return empty($results) ? [] : $this->rowsToPublication($results);
    }

    /**
     * @param string|null $status
     * @param string|null $query
     */
    public function countWhereMatching($status, $query): int
    {
        $query = $this->buildQuery($status, $query);
        $query = $this->transformSelectToCount($query);

        return (int) $this->wpdb->get_var($query);
    }

    public function getPublicationsPerStatus(): array
    {
        $rows = $this->wpdb->get_results(
            "\n            SELECT status, COUNT(*) AS total\n            FROM {$this->tableName}\n            GROUP BY status\n            ORDER BY status",
            \ARRAY_A
        );
        $publicationsPerStatusLevel = [];
        foreach ($rows as $row) {
            $publicationsPerStatusLevel[$row['status']] = (int) $row['total'];
        }

        return $publicationsPerStatusLevel;
    }
}
