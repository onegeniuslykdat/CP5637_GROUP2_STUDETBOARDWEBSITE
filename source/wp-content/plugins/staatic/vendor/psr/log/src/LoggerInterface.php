<?php

namespace Staatic\Vendor\Psr\Log;

use Stringable;
interface LoggerInterface
{
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function emergency($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function alert($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function critical($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function error($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function warning($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function notice($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function info($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function debug($message, $context = []): void;
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = []): void;
}
