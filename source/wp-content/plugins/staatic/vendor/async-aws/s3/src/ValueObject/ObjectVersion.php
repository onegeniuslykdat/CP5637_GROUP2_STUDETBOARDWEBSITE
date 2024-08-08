<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectVersionStorageClass;
final class ObjectVersion
{
    private $etag;
    private $checksumAlgorithm;
    private $size;
    private $storageClass;
    private $key;
    private $versionId;
    private $isLatest;
    private $lastModified;
    private $owner;
    private $restoreStatus;
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->size = $input['Size'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->isLatest = $input['IsLatest'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
        $this->restoreStatus = isset($input['RestoreStatus']) ? RestoreStatus::create($input['RestoreStatus']) : null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getChecksumAlgorithm(): array
    {
        return $this->checksumAlgorithm ?? [];
    }
    public function getEtag(): ?string
    {
        return $this->etag;
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
    public function getRestoreStatus(): ?RestoreStatus
    {
        return $this->restoreStatus;
    }
    public function getSize(): ?int
    {
        return $this->size;
    }
    public function getStorageClass(): ?string
    {
        return $this->storageClass;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
}
