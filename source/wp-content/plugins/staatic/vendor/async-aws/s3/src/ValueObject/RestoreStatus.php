<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class RestoreStatus
{
    private $isRestoreInProgress;
    private $restoreExpiryDate;
    public function __construct(array $input)
    {
        $this->isRestoreInProgress = $input['IsRestoreInProgress'] ?? null;
        $this->restoreExpiryDate = $input['RestoreExpiryDate'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getIsRestoreInProgress(): ?bool
    {
        return $this->isRestoreInProgress;
    }
    public function getRestoreExpiryDate(): ?DateTimeImmutable
    {
        return $this->restoreExpiryDate;
    }
}
