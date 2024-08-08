<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cli;

use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;
use Staatic\WordPress\Logging\LoggerInterface;
use Staatic\WordPress\Migrations\MigrationCoordinatorFactory;
use Staatic\WordPress\Service\Formatter;
use WP_CLI;
use function WP_CLI\Utils\get_flag_value;

class MigrateCommand
{
    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var MigrationCoordinatorFactory
     */
    protected $coordinatorFactory;

    /**
     * @var bool
     */
    private $force = \false;

    /**
     * @param mixed $logger
     */
    public function __construct($logger, Formatter $formatter, MigrationCoordinatorFactory $coordinatorFactory)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
        $this->coordinatorFactory = $coordinatorFactory;
    }

    /**
     * Migrates database for plugin upgrades/downgrades.
     *
     * ## OPTIONS
     *
     * <target>
     * : The target version to migrate to.
     *
     * [--namespace=<namespace>]
     * : The migration namespace.
     * ---
     * default: all
     * options:
     *   - all
     *   - staatic
     * ---
     *
     * [--[no-]force]
     * : Whether or not to force migrating, even if another migration is in progress.
     * ---
     * default: false
     * ---
     *
     * [--[no-]verbose]
     * : Whether or not to output logs during migration.
     * ---
     * default: false
     * ---
     *
     * ## EXAMPLES
     *
     *     wp staatic migrate 1.3.0 --namespace=staatic
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args): void
    {
        [$targetVersion] = $args;
        $namespace = $assoc_args['namespace'];
        $this->force = get_flag_value($assoc_args, 'force', \false);
        $verbose = get_flag_value($assoc_args, 'verbose', \false);
        if ($verbose && $this->logger instanceof LoggerInterface) {
            $this->logger->enableConsoleLogger();
        }
        $namespaces = ($namespace === 'all') ? $this->namespaces() : [$namespace];
        foreach ($namespaces as $namespace) {
            $this->migrateNamespace($namespace, $targetVersion);
        }
    }

    private function migrateNamespace(string $namespace, string $targetVersion): void
    {
        $coordinator = ($this->coordinatorFactory)($namespace, $targetVersion);
        $coordinator->setLogger($this->logger);
        $installedVersion = $coordinator->status('version');
        if ($installedVersion === $targetVersion) {
            WP_CLI::error("Database ({$namespace}) is already at target version ({$targetVersion}).");
        }
        if ($coordinator->isMigrating() && !$this->force) {
            WP_CLI::error("Database ({$namespace}) is already being upgraded; use --force flag to force migration.");
        }
        $migrationSuccessful = $coordinator->migrate();
        if (!$migrationSuccessful) {
            $status = $coordinator->status();
            WP_CLI::error("Database ({$namespace}) migration failed with error: {$status['error']['message']}");
        }
        WP_CLI::success("Database ({$namespace}) successfully migrated from {$installedVersion} to {$targetVersion}.");
    }

    protected function namespaces(): array
    {
        return ['staatic'];
    }
}
