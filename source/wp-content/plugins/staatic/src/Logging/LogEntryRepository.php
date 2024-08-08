<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use DateTimeImmutable;
use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use wpdb;

final class LogEntryRepository
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(wpdb $wpdb, string $tableName = 'staatic_log_entries')
    {
        $this->wpdb = $wpdb;
        $this->tableName = $wpdb->prefix . $tableName;
    }

    public function nextId(): string
    {
        return (string) Uuid::uuid4();
    }

    public function add(LogEntry $logEntry): void
    {
        $context = $logEntry->context();
        $publicationId = isset($context['publicationId']) ? $context['publicationId'] : null;
        $this->wpdb->insert($this->tableName, [
            'uuid' => $logEntry->id(),
            'log_date' => $logEntry->date()->format('Y-m-d H:i:s'),
            'log_level' => $logEntry->level(),
            'message' => $logEntry->message(),
            'context' => $context ? json_encode($context, \JSON_UNESCAPED_SLASHES) : null,
            'publication_uuid' => $publicationId ?: null
        ]);
    }

    private function rowToLogEntry(array $row): LogEntry
    {
        return new LogEntry((string) Uuid::fromBytes($row['uuid']), new DateTimeImmutable(
            $row['log_date']
        ), $row['log_level'], $row['message'], $row['context'] ? json_decode(
            $row['context'],
            \true
        ) : null);
    }

    // Plugin specific methods
    public function deleteOlderThan(int $numDays, array $excludePublicationIds = []): void
    {
        $excludePublicationIds = array_map(function ($publicationId) {
            return sprintf("UNHEX(REPLACE('%s', '-', ''))", esc_sql($publicationId));
        }, $excludePublicationIds);
        $this->wpdb->query(
            $this->wpdb->prepare("\n                DELETE FROM {$this->tableName}\n                WHERE log_date < (NOW() - INTERVAL %d DAY)" . (empty($excludePublicationIds) ? '' : ("\n                    AND publication_uuid NOT IN (" . implode(
                ', ',
                $excludePublicationIds
            ) . ")")), $numDays)
        );
    }

    /**
     * @return LogEntry[]
     */
    public function findByPublicationId(string $publicationId): array
    {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "\n                SELECT *\n                FROM {$this->tableName}\n                WHERE publication_uuid = UNHEX(REPLACE(%s, '-', ''))\n                ORDER BY id DESC",
                $publicationId
            ),
            \ARRAY_A
        );

        return array_map(function ($row) {
            return $this->rowToLogEntry($row);
        }, $results);
    }

    /**
     * @return LogEntry[]
     */
    public function findLatestByPublicationId(string $publicationId, int $limit = 200): array
    {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "\n                SELECT *\n                FROM {$this->tableName}\n                WHERE publication_uuid = UNHEX(REPLACE(%s, '-', '')) AND log_level != %s\n                ORDER BY id DESC\n                LIMIT %d",
                $publicationId,
                LogLevel::DEBUG,
                $limit
            ),
            \ARRAY_A
        );

        return array_map(function ($row) {
            return $this->rowToLogEntry($row);
        }, $results);
    }

    /**
     * @return Result[]
     */
    public function findWhereMatching(
        string $publicationId,
        $levels,
        ?string $query,
        int $limit,
        int $offset,
        ?string $orderBy,
        ?string $direction
    ): array
    {
        $levels = is_array($levels) ? $levels : ($levels ? [$levels] : []);
        $orderBy = $orderBy ?: null;
        $direction = $direction ?: 'DESC';
        $logEntries = $this->wpdb->get_results(
            "\n            SELECT *\n            FROM {$this->tableName}\n            WHERE publication_uuid = UNHEX(REPLACE('" . esc_sql(
                $publicationId
            ) . "', '-', ''))" . ((!empty($levels)) ? "\n            AND log_level IN ('" . implode(
                "', '",
                esc_sql($levels)
            ) . "')" : "") . ($query ? "\n                AND message LIKE '%" . esc_sql(
                $this->wpdb->esc_like($query)
            ) . "%'" : "") . "\n            ORDER BY " . ($orderBy ? "{$orderBy} {$direction}, " : "") . "id {$direction}\n            LIMIT {$limit} OFFSET {$offset}",
            \ARRAY_A
        );

        return array_map(function ($row) {
            return $this->rowToLogEntry($row);
        }, $logEntries);
    }

    public function countWhereMatching(string $publicationId, ?string $level = null, ?string $query = null): int
    {
        return (int) $this->wpdb->get_var(
            "\n            SELECT COUNT(*)\n            FROM {$this->tableName}\n            WHERE publication_uuid = UNHEX(REPLACE('" . esc_sql(
                $publicationId
            ) . "', '-', ''))" . ($level ? "\n                AND log_level = '" . esc_sql(
                $level
            ) . "'" : '') . ($query ? "\n                AND message LIKE '%" . esc_sql(
                $this->wpdb->esc_like($query)
            ) . "%'" : "")
        );
    }

    public function getLogEntriesPerLevel(string $publicationId): array
    {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "\n                SELECT log_level, COUNT(*) AS total\n                FROM {$this->tableName}\n                WHERE publication_uuid = UNHEX(REPLACE(%s, '-', ''))\n                GROUP BY log_level\n                ORDER BY log_level",
                $publicationId
            ),
            \ARRAY_A
        );
        $logEntriesPerStatusLevel = [];
        foreach ($rows as $row) {
            $logEntriesPerStatusLevel[$row['log_level']] = (int) $row['total'];
        }

        return $logEntriesPerStatusLevel;
    }
}
