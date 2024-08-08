<?php

namespace Staatic\Vendor\Psr\Log;

use Stringable;
trait LoggerTrait
{
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function emergency($message, $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function alert($message, $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function critical($message, $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function error($message, $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function warning($message, $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function notice($message, $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function info($message, $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function debug($message, $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    abstract public function log($level, $message, $context = []): void;
}
