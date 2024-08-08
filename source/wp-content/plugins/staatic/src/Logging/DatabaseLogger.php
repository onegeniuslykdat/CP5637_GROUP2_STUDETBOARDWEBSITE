<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Stringable;
use DateTimeImmutable;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\LoggerTrait;

final class DatabaseLogger implements LoggerInterface
{
    /**
     * @var LogEntryRepository
     */
    private $repository;

    use LoggerTrait;

    public function __construct(LogEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = []): void
    {
        if (isset($context['failure'])) {
            $context['failure'] = (string) $context['failure'];
        }
        $this->repository->add(
            new LogEntry($this->repository->nextId(), new DateTimeImmutable(), $level, $message, $context)
        );
    }
}
