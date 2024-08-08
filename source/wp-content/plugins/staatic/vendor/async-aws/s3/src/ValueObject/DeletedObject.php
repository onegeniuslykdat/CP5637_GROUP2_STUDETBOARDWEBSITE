<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class DeletedObject
{
    private $key;
    private $versionId;
    private $deleteMarker;
    private $deleteMarkerVersionId;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->deleteMarker = $input['DeleteMarker'] ?? null;
        $this->deleteMarkerVersionId = $input['DeleteMarkerVersionId'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getDeleteMarker(): ?bool
    {
        return $this->deleteMarker;
    }
    public function getDeleteMarkerVersionId(): ?string
    {
        return $this->deleteMarkerVersionId;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
}
