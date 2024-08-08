<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use InvalidArgumentException;
use RuntimeException;
use wpdb;

class MigrationCoordinatorFactory
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $pluginVersion;

    public function __construct(wpdb $wpdb, string $pluginVersion)
    {
        $this->wpdb = $wpdb;
        $this->pluginVersion = $pluginVersion;
    }

    public function __invoke(string $namespace, ?string $targetVersion = null): MigrationCoordinator
    {
        $migrator = new Migrator($this->wpdb, $this->determineMigrationsDir($namespace));
        $targetVersion = $this->normalizeTargetVersion($targetVersion ?: $this->pluginVersion);

        return new MigrationCoordinator($migrator, $namespace, $targetVersion, $this->wpdb);
    }

    /**
     * @param string $namespace
     */
    protected function determineMigrationsDir($namespace): string
    {
        switch ($namespace) {
            case 'staatic':
                return __DIR__ . '/../../migrations';
            default:
                return new InvalidArgumentException("Unsupported namespace: {$namespace}.");
        }
    }

    private function normalizeTargetVersion(string $version): string
    {
        if (preg_match('~^\d+\.\d+\.\d+(?:-beta\d+)?~', $version, $match) === 0) {
            throw new RuntimeException("Plugin version has an invalid format: '{$version}'");
        }

        return $match[0];
    }
}
