<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class CompletedPart
{
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $partNumber;
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
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
    public function getPartNumber(): ?int
    {
        return $this->partNumber;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->etag) {
            $node->appendChild($document->createElement('ETag', $v));
        }
        if (null !== $v = $this->checksumCrc32) {
            $node->appendChild($document->createElement('ChecksumCRC32', $v));
        }
        if (null !== $v = $this->checksumCrc32C) {
            $node->appendChild($document->createElement('ChecksumCRC32C', $v));
        }
        if (null !== $v = $this->checksumSha1) {
            $node->appendChild($document->createElement('ChecksumSHA1', $v));
        }
        if (null !== $v = $this->checksumSha256) {
            $node->appendChild($document->createElement('ChecksumSHA256', $v));
        }
        if (null !== $v = $this->partNumber) {
            $node->appendChild($document->createElement('PartNumber', (string) $v));
        }
    }
}
