<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\WordPress\Logging\Logger;
use Staatic\WordPress\Setting\Advanced\LoggingLevelSetting;

final class LoggerFactory
{
    /**
     * @var LoggingLevelSetting
     */
    private $loggingLevel;

    /**
     * @var LoggerInterface
     */
    private $databaseLogger;

    /**
     * @var LoggerInterface
     */
    private $consoleLogger;

    public function __construct(LoggingLevelSetting $loggingLevel, LoggerInterface $databaseLogger, LoggerInterface $consoleLogger)
    {
        $this->loggingLevel = $loggingLevel;
        $this->databaseLogger = $databaseLogger;
        $this->consoleLogger = $consoleLogger;
    }

    public function __invoke(): LoggerInterface
    {
        $loggingLevel = $this->loggingLevel->value();
        if ($loggingLevel === LoggingLevelSetting::VALUE_DISABLED) {
            return new NullLogger();
        }
        switch ($loggingLevel) {
            case LoggingLevelSetting::VALUE_MINIMAL:
                $minimumLevel = LogLevel::NOTICE;

                break;
            case LoggingLevelSetting::VALUE_DETAILED:
                $minimumLevel = LogLevel::INFO;

                break;
            case LoggingLevelSetting::VALUE_EXTENSIVE:
                $minimumLevel = LogLevel::DEBUG;

                break;
            default:
                $minimumLevel = LogLevel::INFO;

                break;
        }

        return new Logger($this->databaseLogger, $this->consoleLogger, $minimumLevel);
    }
}
