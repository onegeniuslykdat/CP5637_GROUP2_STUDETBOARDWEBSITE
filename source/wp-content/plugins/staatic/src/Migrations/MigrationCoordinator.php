<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use Exception;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\WordPress\Util\TimeLimit;
use wpdb;

final class MigrationCoordinator implements LoggerAwareInterface
{
    /**
     * @var Migrator
     */
    private $migrator;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $targetVersion;

    /**
     * @var wpdb
     */
    private $wpdb;

    use LoggerAwareTrait;

    /** @var string */
    private const MIGRATION_OPTION_NAME = '%s_database_version';

    /** @var int */
    private const TIME_LIMIT = 3600;

    /**
     * @var mixed[]|null
     */
    private $status;

    public function __construct(Migrator $migrator, string $namespace, string $targetVersion, wpdb $wpdb)
    {
        $this->migrator = $migrator;
        $this->namespace = $namespace;
        $this->targetVersion = $targetVersion;
        $this->wpdb = $wpdb;
        $this->logger = new NullLogger();
    }

    /**
     * @param string|null $key
     */
    public function status($key = null)
    {
        if ($this->status === null) {
            $status = get_option($this->optionName());
            if (is_string($status) && !empty($status)) {
                $this->status = [
                    'version' => $status
                ];
            } elseif (!is_array($status) || !isset($status['version'])) {
                $this->status = [
                    'version' => '0.0.0'
                ];
            } else {
                $this->status = $status;
            }
        }

        return $key ? $this->status[$key] : $this->status;
    }

    public function hasMigrationFailed(): bool
    {
        $status = $this->status();

        return array_key_exists('error', $status);
    }

    public function isMigrating(): bool
    {
        $status = $this->status();
        if (!array_key_exists('lock', $status)) {
            return \false;
        }

        return strtotime('-30 minutes') <= $status['lock'];
    }

    public function shouldMigrate(): bool
    {
        if ($this->isMigrating()) {
            return \false;
        }
        $installedVersion = $this->status('version');

        return version_compare($installedVersion, $this->targetVersion, '<');
    }

    public function migrate(): bool
    {
        if (!$this->lockMigration()) {
            return \false;
        }
        $this->migrator->setLogger($this->logger);
        $installedVersion = $this->status('version');
        if (!$suppressErrors = $this->wpdb->suppress_errors) {
            $this->wpdb->suppress_errors();
        }
        TimeLimit::setTimeLimit(self::TIME_LIMIT);

        try {
            $this->logger->notice("Migrating {$this->namespace} from {$installedVersion} to {$this->targetVersion}.");
            $this->wpdb->query('START TRANSACTION');
            $this->migrator->migrate($installedVersion, $this->targetVersion);
            $this->wpdb->query('COMMIT');
            $this->clearTransientCache();
        } catch (Exception $e) {
            $this->logger->error("Migrating {$this->namespace} failed: {$e->getMessage()}");
            $this->wpdb->query('ROLLBACK');
            $this->migrationFailed($e->getMessage());

            return \false;
        } finally {
            if (!$suppressErrors) {
                $this->wpdb->suppress_errors(\false);
            }
        }
        $this->logger->notice("Migrating {$this->namespace} was successful.");
        $this->migrationSuccessful();

        return \true;
    }

    public function reset(): bool
    {
        if (!$this->lockMigration()) {
            return \false;
        }
        $this->migrator->setLogger($this->logger);
        if (!$suppressErrors = $this->wpdb->suppress_errors) {
            $this->wpdb->suppress_errors();
        }
        TimeLimit::setTimeLimit(self::TIME_LIMIT);

        try {
            $this->migrator->reset();
            $this->clearTransientCache();
        } catch (Exception $e) {
            $this->logger->error("Resetting {$this->namespace} failed: {$e->getMessage()}");
            $this->migrationFailed($e->getMessage());

            return \false;
        } finally {
            if (!$suppressErrors) {
                $this->wpdb->suppress_errors(\false);
            }
        }
        $this->logger->notice("Resetting {$this->namespace} was successful.");
        $this->resetSuccessful();

        return \true;
    }

    private function clearTransientCache(): void
    {
        $this->wpdb->query(
            $this->wpdb->prepare("DELETE FROM {$this->wpdb->prefix}options WHERE option_name LIKE %s", $this->wpdb->esc_like(
                '_transient_staatic_'
            ) . '%')
        );
    }

    private function migrationSuccessful(): void
    {
        $status = $this->status();
        unset($status['lock'], $status['error']);
        $status['version'] = $this->targetVersion;
        $this->setStatus($status);
    }

    private function resetSuccessful(): void
    {
        $this->setStatus(null);
    }

    private function migrationFailed(string $message): void
    {
        $status = $this->status();
        unset($status['lock']);
        $status['error'] = [
            'time' => time(),
            'version' => $this->targetVersion,
            'message' => $message
        ];
        $this->setStatus($status);
    }

    private function lockMigration(): bool
    {
        $status = $this->status();
        $status['lock'] = time();

        return $this->setStatus($status);
    }

    private function setStatus($status): bool
    {
        $this->status = $status;

        return update_option($this->optionName(), $status);
    }

    private function optionName(): string
    {
        return sprintf(self::MIGRATION_OPTION_NAME, $this->namespace);
    }
}
