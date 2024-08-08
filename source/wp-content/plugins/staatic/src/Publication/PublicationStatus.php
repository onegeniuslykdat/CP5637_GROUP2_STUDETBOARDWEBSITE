<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use InvalidArgumentException;
use RuntimeException;

final class PublicationStatus
{
    /**
     * @var string
     */
    private $status;

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_FAILED = 'failed';

    private function __construct(string $status)
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public static function create($status): self
    {
        if (!in_array(
            $status,
            [self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_FINISHED,
            self::STATUS_CANCELED,
            self::STATUS_FAILED
        ])) {
            throw new InvalidArgumentException(sprintf('Invalid status supplied: %s', $status));
        }

        return new self($status);
    }

    public function status(): string
    {
        return $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function label(): string
    {
        $labels = self::labels();
        if (!isset($labels[$this->status])) {
            throw new RuntimeException(sprintf('Unknown status %s', $this->status));
        }

        return $labels[$this->status];
    }

    public static function labels(): array
    {
        return [
            self::STATUS_PENDING => __('Pending', 'staatic'),
            self::STATUS_IN_PROGRESS => __('In Progress', 'staatic'),
            self::STATUS_FINISHED => __('Finished', 'staatic'),
            self::STATUS_CANCELED => __('Canceled', 'staatic'),
            self::STATUS_FAILED => __('Failed', 'staatic')
        ];
    }
}
