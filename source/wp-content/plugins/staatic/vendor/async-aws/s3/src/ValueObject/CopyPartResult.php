<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class CopyPartResult
{
    private $etag;
    private $lastModified;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
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
}
