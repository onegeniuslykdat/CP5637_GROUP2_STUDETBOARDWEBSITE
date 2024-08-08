<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class DeleteMarkerEntry
{
    private $owner;
    private $key;
    private $versionId;
    private $isLatest;
    private $lastModified;
    public function __construct(array $input)
    {
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->isLatest = $input['IsLatest'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getIsLatest(): ?bool
    {
        return $this->isLatest;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getLastModified(): ?DateTimeImmutable
    {
        return $this->lastModified;
    }
    public function getOwner(): ?Owner
    {
        return $this->owner;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
}
