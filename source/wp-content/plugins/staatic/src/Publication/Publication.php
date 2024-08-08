<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Framework\Build;
use Staatic\Framework\Deployment;
use WP_User;

final class Publication
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var DateTimeInterface
     */
    private $dateCreated;

    /**
     * @var Build
     */
    private $build;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var bool
     */
    private $isPreview = \false;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * @var mixed[]
     */
    private $metadata = [];

    /**
     * @var DateTimeInterface|null
     */
    private $dateFinished;

    /**
     * @var string|null
     */
    private $currentTask;

    /** @var int */
    public const TIME_LIMIT_IN_HOURS = 4;

    /**
     * @var PublicationStatus
     */
    private $status;

    public function __construct(string $id, DateTimeInterface $dateCreated, Build $build, Deployment $deployment, bool $isPreview = \false, ?int $userId = null, array $metadata = [], PublicationStatus $status = null, ?DateTimeInterface $dateFinished = null, ?string $currentTask = null)
    {
        $this->id = $id;
        $this->dateCreated = $dateCreated;
        $this->build = $build;
        $this->deployment = $deployment;
        $this->isPreview = $isPreview;
        $this->userId = $userId;
        $this->metadata = $metadata;
        $this->dateFinished = $dateFinished;
        $this->currentTask = $currentTask;
        $this->status = $status ?? PublicationStatus::create(PublicationStatus::STATUS_PENDING);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function dateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function build(): Build
    {
        return $this->build;
    }

    public function deployment(): Deployment
    {
        return $this->deployment;
    }

    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    public function publisher(): ?WP_User
    {
        if (!$this->userId) {
            return null;
        }

        return get_userdata($this->userId) ?: null;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function metadataByKey(string $key)
    {
        return $this->metadata[$key] ?? null;
    }

    public function status(): PublicationStatus
    {
        return $this->status;
    }

    public function dateFinished(): ?DateTimeInterface
    {
        return $this->dateFinished;
    }

    public function currentTask(): ?string
    {
        return $this->currentTask;
    }

    public function setStatus(PublicationStatus $status): void
    {
        $this->status = $status;
    }

    public function setCurrentTask(?string $currentTask): void
    {
        $this->currentTask = $currentTask;
    }

    public function markInProgress(): void
    {
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_IN_PROGRESS);
    }

    public function markCanceled(): void
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_CANCELED);
        $this->dateFinished = new DateTimeImmutable();
    }

    public function markFailed(): void
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_FAILED);
        $this->dateFinished = new DateTimeImmutable();
    }

    public function markFinished(): void
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_FINISHED);
        $this->dateFinished = new DateTimeImmutable();
    }

    public function updateMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
