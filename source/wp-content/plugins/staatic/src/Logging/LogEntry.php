<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use ReturnTypeWillChange;
use DateTimeInterface;
use JsonSerializable;

final class LogEntry implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var string
     */
    private $level;

    /**
     * @var string
     */
    private $message;

    /**
     * @var mixed[]|null
     */
    private $context = [];

    public function __construct(string $id, DateTimeInterface $date, string $level, string $message, ?array $context = [])
    {
        $this->id = $id;
        $this->date = $date;
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function date(): DateTimeInterface
    {
        return $this->date;
    }

    public function level(): string
    {
        return $this->level;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function context(): ?array
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('c'),
            'level' => $this->level,
            'message' => $this->message,
            'context' => $this->context
        ];
    }
}
