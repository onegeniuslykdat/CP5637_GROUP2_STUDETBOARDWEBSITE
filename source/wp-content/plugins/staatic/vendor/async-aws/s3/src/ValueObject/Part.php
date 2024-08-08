<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class Part
{
    private $partNumber;
    private $lastModified;
    private $etag;
    private $size;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    public function __construct(array $input)
    {
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->etag = $input['ETag'] ?? null;
        $this->size = $input['Size'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getChecksumCrc32(): ?string
    {
        return $this->checksumCrc32;
    }
    public function getChecksumCrc32C(): ?string
    {
        return $this->checksumCrc32C;
    }
    public function getChecksumSha1(): ?string
    {
        return $this->checksumSha1;
    }
    public function getChecksumSha256(): ?string
    {
        return $this->checksumSha256;
    }
    public function getEtag(): ?string
    {
        return $this->etag;
    }
    public function getLastModified(): ?DateTimeImmutable
    {
        return $this->lastModified;
    }
    public function getPartNumber(): ?int
    {
        return $this->partNumber;
    }
    public function getSize(): ?int
    {
        return $this->size;
    }
}
