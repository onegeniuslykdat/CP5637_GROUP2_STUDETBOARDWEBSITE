<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface, Contextable
{
    public function consoleLoggerEnabled(): bool;

    public function enableConsoleLogger(): void;

    public function disableConsoleLogger(): void;

    public function databaseLoggerEnabled(): bool;

    public function enableDatabaseLogger(): void;

    public function disableDatabaseLogger(): void;
}
