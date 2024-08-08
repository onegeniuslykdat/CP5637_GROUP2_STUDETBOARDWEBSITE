<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
final class MultipartUpload
{
    private $uploadId;
    private $key;
    private $initiated;
    private $storageClass;
    private $owner;
    private $initiator;
    private $checksumAlgorithm;
    public function __construct(array $input)
    {
        $this->uploadId = $input['UploadId'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->initiated = $input['Initiated'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
        $this->initiator = isset($input['Initiator']) ? Initiator::create($input['Initiator']) : null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getChecksumAlgorithm(): ?string
    {
        return $this->checksumAlgorithm;
    }
    public function getInitiated(): ?DateTimeImmutable
    {
        return $this->initiated;
    }
    public function getInitiator(): ?Initiator
    {
        return $this->initiator;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getOwner(): ?Owner
    {
        return $this->owner;
    }
    public function getStorageClass(): ?string
    {
        return $this->storageClass;
    }
    public function getUploadId(): ?string
    {
        return $this->uploadId;
    }
}
