<?php

namespace Staatic\Framework\Logger;

use Stringable;
use DateTimeImmutable;
use Error;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
class StreamLogger implements LoggerInterface
{
    use LoggerTrait;
    private const FORMAT = "[%s %s] %s%s\n";
    private const CONTEXT_FORMAT = "[%s] ";
    /**
     * @var StreamInterface
     */
    private $logStream;
    public function __construct(StreamInterface $logStream)
    {
        if (!$logStream->isWritable()) {
            throw new InvalidArgumentException('Log stream is not writable');
        }
        $this->logStream = $logStream;
    }
    /**
     * @param string $path
     */
    public static function createFromFile($path): self
    {
        try {
            $handle = fopen($path, 'a');
        } catch (Error $error) {
            throw new InvalidArgumentException("Unable to open file for writing in {$path}: {$error->getMessage()}");
        }
        return new self(Utils::streamFor($handle));
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = []): void
    {
        $sourceContext = $this->getSourceContext();
        $source = $this->getShortClassName($sourceContext['sourceClass']);
        $context = array_merge(['source' => $source], $context);
        $date = (new DateTimeImmutable())->format('H:i:s.u');
        $memory = number_format(memory_get_usage() / 1024 / 1024, 3) . ' MB';
        $contextString = (count($context) > 0) ? sprintf(self::CONTEXT_FORMAT, implode('] [', $context)) : '';
        $this->logStream->write(sprintf(self::FORMAT, $date, $memory, $contextString, $message));
    }
}
