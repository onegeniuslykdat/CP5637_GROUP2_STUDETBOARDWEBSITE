<?php

namespace Staatic\Framework;

use DateTimeImmutable;
use DateTimeInterface;
final class Deployment
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $buildId;
    /**
     * @var DateTimeInterface|null
     */
    private $dateStarted;
    /**
     * @var DateTimeInterface|null
     */
    private $dateFinished;
    /**
     * @var int
     */
    private $numResultsTotal = 0;
    /**
     * @var int
     */
    private $numResultsDeployable = 0;
    /**
     * @var int
     */
    private $numResultsDeployed = 0;
    /**
     * @var mixed[]|null
     */
    private $metadata;
    /**
     * @var DateTimeInterface
     */
    private $dateCreated;
    public function __construct(string $id, string $buildId, ?DateTimeInterface $dateCreated = null, ?DateTimeInterface $dateStarted = null, ?DateTimeInterface $dateFinished = null, int $numResultsTotal = 0, int $numResultsDeployable = 0, int $numResultsDeployed = 0, ?array $metadata = null)
    {
        $this->id = $id;
        $this->buildId = $buildId;
        $this->dateStarted = $dateStarted;
        $this->dateFinished = $dateFinished;
        $this->numResultsTotal = $numResultsTotal;
        $this->numResultsDeployable = $numResultsDeployable;
        $this->numResultsDeployed = $numResultsDeployed;
        $this->metadata = $metadata;
        $this->dateCreated = $dateCreated ?: new DateTimeImmutable();
    }
    public function __toString()
    {
        return (string) $this->id;
    }
    public function id(): string
    {
        return $this->id;
    }
    public function buildId(): string
    {
        return $this->buildId;
    }
    public function dateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }
    public function dateStarted(): ?DateTimeInterface
    {
        return $this->dateStarted;
    }
    public function isStarted(): bool
    {
        return (bool) $this->dateStarted;
    }
    public function dateFinished(): ?DateTimeInterface
    {
        return $this->dateFinished;
    }
    public function isFinished(): bool
    {
        return (bool) $this->dateFinished;
    }
    public function numResultsTotal(): int
    {
        return $this->numResultsTotal;
    }
    public function numResultsDeployable(): int
    {
        return $this->numResultsDeployable;
    }
    public function numResultsDeployed(): int
    {
        return $this->numResultsDeployed;
    }
    public function metadata(): ?array
    {
        return $this->metadata;
    }
    /**
     * @param int $numResultsTotal
     * @param int $numResultsDeployable
     * @param mixed[]|null $metadata
     */
    public function deployStarted($numResultsTotal, $numResultsDeployable, $metadata): void
    {
        $this->dateStarted = new DateTimeImmutable();
        $this->numResultsTotal = $numResultsTotal;
        $this->numResultsDeployable = $numResultsDeployable;
        $this->metadata = $metadata;
    }
    public function deployFinished(): void
    {
        $this->dateFinished = new DateTimeImmutable();
    }
    /**
     * @param int $numResultsDeployed
     */
    public function deployedResults($numResultsDeployed): void
    {
        $this->numResultsDeployed = $numResultsDeployed;
    }
}
