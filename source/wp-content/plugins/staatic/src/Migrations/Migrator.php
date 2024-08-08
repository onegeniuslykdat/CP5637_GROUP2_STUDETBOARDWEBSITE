<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use GlobIterator;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use wpdb;

final class Migrator implements LoggerAwareInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $migrationsDir;

    use LoggerAwareTrait;

    /** @var string */
    private const DIRECTION_UP = 'up';

    /** @var string */
    private const DIRECTION_DOWN = 'down';

    public function __construct(wpdb $wpdb, string $migrationsDir)
    {
        $this->wpdb = $wpdb;
        $this->migrationsDir = $migrationsDir;
        $this->logger = new NullLogger();
        $this->migrationsDir = rtrim($this->migrationsDir, '/\\');
    }

    /**
     * @param string $installedVersion
     * @param string|null $targetVersion
     */
    public function migrate($installedVersion, $targetVersion = null): void
    {
        $direction = $this->findDirection($targetVersion, $installedVersion);
        if ($installedVersion === '0.0.0') {
            $migrations = [$this->setupMigration()];
        } else {
            $migrations = iterator_to_array($this->findMigrations());
            $migrations = $this->filterMigrations($migrations, $targetVersion, $installedVersion, $direction);
            $migrations = $this->sortMigrations($migrations, $direction);
        }
        // dd(
        //     sprintf("INSTALLED: %s, TARGET: %s, DIRECTION: %s", $installedVersion, $targetVersion, $direction),
        //     $migrations,
        // );
        $this->executeMigrations($migrations, $direction);
    }

    public function reset(): void
    {
        $this->executeMigrations([$this->setupMigration()], self::DIRECTION_DOWN);
    }

    private function executeMigrations(iterable $migrations, string $direction): void
    {
        foreach ($migrations as $migrationSpec) {
            $this->logger->info("Applying migration '{$migrationSpec['name']}'.");
            if ($direction === self::DIRECTION_UP) {
                $migrationSpec['instance']->up($this->wpdb);
            } else {
                $migrationSpec['instance']->down($this->wpdb);
            }
        }
    }

    private function findDirection(string $targetVersion = null, ?string $installedVersion = null): string
    {
        if ($installedVersion === '0.0.0') {
            return self::DIRECTION_UP;
        }
        if (version_compare($installedVersion, $targetVersion, '==')) {
            throw new InvalidArgumentException('Installed version and target version cannot be the same!');
        }

        return version_compare($installedVersion, $targetVersion, '<') ? self::DIRECTION_UP : self::DIRECTION_DOWN;
    }

    private function filterMigrations(
        array $migrations,
        string $targetVersion,
        ?string $installedVersion,
        string $direction
    ): array
    {
        $filter = ($direction === self::DIRECTION_UP) ? function ($migrationSpec) use (
            $targetVersion,
            $installedVersion
        ) {
            return ($installedVersion ? version_compare(
                $migrationSpec['version'],
                $installedVersion,
                '>'
            ) : \true) && version_compare(
                $migrationSpec['version'],
                $targetVersion,
                '<='
            );
        } : function ($migrationSpec) use ($targetVersion, $installedVersion) {
            return version_compare($migrationSpec['version'], $installedVersion, '<=') && version_compare(
                $migrationSpec['version'],
                $targetVersion,
                '>'
            );
        };

        return array_filter($migrations, $filter);
    }

    private function sortMigrations(array $migrations, string $direction): array
    {
        $comparator = ($direction === self::DIRECTION_UP) ? function ($a, $b) {
            return $this->compareMigrations($a, $b);
        } : function ($a, $b) {
            return $this->compareMigrations($b, $a);
        };
        uasort($migrations, $comparator);

        return $migrations;
    }

    private function compareMigrations(array $a, array $b): int
    {
        $versionCompare = version_compare($a['version'], $b['version']);
        if ($versionCompare !== 0) {
            return $versionCompare;
        }

        return $a['name'] <=> $b['name'];
    }

    private function setupMigration(): array
    {
        $instance = require "{$this->migrationsDir}/setup.php";

        return [
            'version' => null,
            'name' => 'setup',
            'instance' => $instance
        ];
    }

    private function findMigrations(): iterable
    {
        if (!is_dir($this->migrationsDir)) {
            throw new RuntimeException("Migration directory does not exist in {$this->migrationsDir}");
        }
        $pattern = "{$this->migrationsDir}/v*.php";
        $iterator = new GlobIterator($pattern);
        foreach ($iterator as $fileInfo) {
            if (preg_match('~^v(\d+\.\d+\.\d+(?:-beta\d+)?)-(.+?)\.php$~', $fileInfo->getFilename(), $match) === 0) {
                continue;
            }
            $instance = require $fileInfo->getPathname();
            if (!$instance instanceof MigrationInterface) {
                continue;
            }
            yield [
                'version' => $match[1],
                'name' => $match[2],
                'instance' => $instance
            ];
        }
    }
}
