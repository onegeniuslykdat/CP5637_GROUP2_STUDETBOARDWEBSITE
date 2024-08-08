<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
class CompleteMultipartUploadOutput extends Result
{
    private $location;
    private $bucket;
    private $key;
    private $expiration;
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $serverSideEncryption;
    private $versionId;
    private $sseKmsKeyId;
    private $bucketKeyEnabled;
    private $requestCharged;
    public function getBucket(): ?string
    {
        $this->initialize();
        return $this->bucket;
    }
    public function getBucketKeyEnabled(): ?bool
    {
        $this->initialize();
        return $this->bucketKeyEnabled;
    }
    public function getChecksumCrc32(): ?string
    {
        $this->initialize();
        return $this->checksumCrc32;
    }
    public function getChecksumCrc32C(): ?string
    {
        $this->initialize();
        return $this->checksumCrc32C;
    }
    public function getChecksumSha1(): ?string
    {
        $this->initialize();
        return $this->checksumSha1;
    }
    public function getChecksumSha256(): ?string
    {
        $this->initialize();
        return $this->checksumSha256;
    }
    public function getEtag(): ?string
    {
        $this->initialize();
        return $this->etag;
    }
    public function getExpiration(): ?string
    {
        $this->initialize();
        return $this->expiration;
    }
    public function getKey(): ?string
    {
        $this->initialize();
        return $this->key;
    }
    public function getLocation(): ?string
    {
        $this->initialize();
        return $this->location;
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
    public function getServerSideEncryption(): ?string
    {
        $this->initialize();
        return $this->serverSideEncryption;
    }
    public function getSseKmsKeyId(): ?string
    {
        $this->initialize();
        return $this->sseKmsKeyId;
    }
    public function getVersionId(): ?string
    {
        $this->initialize();
        return $this->versionId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->expiration = $headers['x-amz-expiration'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->location = ($v = $data->Location) ? (string) $v : null;
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->etag = ($v = $data->ETag) ? (string) $v : null;
        $this->checksumCrc32 = ($v = $data->ChecksumCRC32) ? (string) $v : null;
        $this->checksumCrc32C = ($v = $data->ChecksumCRC32C) ? (string) $v : null;
        $this->checksumSha1 = ($v = $data->ChecksumSHA1) ? (string) $v : null;
        $this->checksumSha256 = ($v = $data->ChecksumSHA256) ? (string) $v : null;
    }
}
