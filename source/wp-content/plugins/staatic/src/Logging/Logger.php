<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Stringable;
use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;
use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\Framework\Logger\LoggerTrait;
use Staatic\WordPress\Bootstrap;

final class Logger implements \Staatic\WordPress\Logging\LoggerInterface
{
    /**
     * @var PsrLoggerInterface
     */
    private $databaseLogger;

    /**
     * @var PsrLoggerInterface
     */
    private $consoleLogger;

    /**
     * @var bool
     */
    private $consoleLoggerEnabled = \false;

    /**
     * @var bool
     */
    private $databaseLoggerEnabled = \true;

    use LoggerTrait;

    private const LOG_LEVELS_ORDERED = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY
    ];

    /**
     * @var int
     */
    private $minimumLevelIndex;

    /**
     * @var mixed[]
     */
    private $context = [];

    /**
     * @param mixed $databaseLogger
     * @param mixed $consoleLogger
     */
    public function __construct($databaseLogger, $consoleLogger, string $minimumLevel = LogLevel::DEBUG, bool $consoleLoggerEnabled = \false, bool $databaseLoggerEnabled = \true)
    {
        $this->databaseLogger = $databaseLogger;
        $this->consoleLogger = $consoleLogger;
        $this->consoleLoggerEnabled = $consoleLoggerEnabled;
        $this->databaseLoggerEnabled = $databaseLoggerEnabled;
        $this->minimumLevelIndex = array_search($minimumLevel, self::LOG_LEVELS_ORDERED);
    }

    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = array()): void
    {
        if (!$this->shouldHandle($level)) {
            return;
        }
        $context = array_merge($this->context, $context);
        if (Bootstrap::instance()->isDebug()) {
            $context = array_merge($this->getSourceContext(), [
                'memory' => memory_get_usage()
            ], $context);
        }
        if ($this->consoleLoggerEnabled) {
            $this->consoleLogger->log($level, $message, $context);
        }
        if ($this->databaseLoggerEnabled) {
            $this->databaseLogger->log($level, $message, $context);
        }
    }

    private function shouldHandle($level): bool
    {
        $levelIndex = array_search($level, self::LOG_LEVELS_ORDERED);

        return $levelIndex >= $this->minimumLevelIndex;
    }

    public function consoleLoggerEnabled(): bool
    {
        return $this->consoleLoggerEnabled;
    }

    public function enableConsoleLogger(): void
    {
        $this->consoleLoggerEnabled = \true;
    }

    public function disableConsoleLogger(): void
    {
        $this->consoleLoggerEnabled = \false;
    }

    public function databaseLoggerEnabled(): bool
    {
        return $this->databaseLoggerEnabled;
    }

    public function enableDatabaseLogger(): void
    {
        $this->databaseLoggerEnabled = \true;
    }

    public function disableDatabaseLogger(): void
    {
        $this->databaseLoggerEnabled = \false;
    }

    /**
     * @param mixed[] $context
     */
    public function changeContext($context): void
    {
        $this->context = $context;
    }
}
