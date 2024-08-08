<?php

namespace Staatic\Framework\Logger;

use DateTimeImmutable;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\LogLevel;
use Stringable;
class ConsoleLogger implements LoggerInterface
{
    /**
     * @var bool
     */
    private $includeContext = \false;
    use LoggerTrait;
    private const FORMAT_BASIC = "\x1b[2m[%1\$s %2\$s]\x1b[0m \x1b[%4\$dm%5\$s\x1b[0m\n";
    private const FORMAT_WITH_CONTEXT = "\x1b[2m[%1\$s %2\$s]\x1b[0m %3\$s\x1b[%4\$dm%5\$s\x1b[0m\n";
    private const CONTEXT_FORMAT = "\x1b[2m[%s]\x1b[0m ";
    private const LOG_LEVEL_COLORS = [LogLevel::EMERGENCY => 91, LogLevel::ALERT => 91, LogLevel::CRITICAL => 91, LogLevel::ERROR => 31, LogLevel::WARNING => 31, LogLevel::NOTICE => 36, LogLevel::INFO => 0, LogLevel::DEBUG => 94];
    public function __construct(bool $includeContext = \false)
    {
        $this->includeContext = $includeContext;
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = []): void
    {
        if ($this->includeContext) {
            $sourceContext = $this->getSourceContext();
            $source = $this->getShortClassName($sourceContext['sourceClass']);
            $context = array_merge(['source' => $source], $context);
        }
        $color = self::LOG_LEVEL_COLORS[$level];
        $date = (new DateTimeImmutable())->format('H:i:s.u');
        $memory = number_format(memory_get_usage() / 1024 / 1024, 3) . ' MiB';
        $contextString = (count($context) > 0) ? sprintf(self::CONTEXT_FORMAT, implode('] [', $context)) : '';
        $format = $this->includeContext ? self::FORMAT_WITH_CONTEXT : self::FORMAT_BASIC;
        printf($format, $date, $memory, $contextString, $color, $message);
    }
}
