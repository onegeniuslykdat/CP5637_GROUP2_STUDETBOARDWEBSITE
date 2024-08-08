<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
class UploadPartOutput extends Result
{
    private $serverSideEncryption;
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $sseCustomerAlgorithm;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $bucketKeyEnabled;
    private $requestCharged;
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
    public function getSseCustomerAlgorithm(): ?string
    {
        $this->initialize();
        return $this->sseCustomerAlgorithm;
    }
    public function getSseCustomerKeyMd5(): ?string
    {
        $this->initialize();
        return $this->sseCustomerKeyMd5;
    }
    public function getSseKmsKeyId(): ?string
    {
        $this->initialize();
        return $this->sseKmsKeyId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->etag = $headers['etag'][0] ?? null;
        $this->checksumCrc32 = $headers['x-amz-checksum-crc32'][0] ?? null;
        $this->checksumCrc32C = $headers['x-amz-checksum-crc32c'][0] ?? null;
        $this->checksumSha1 = $headers['x-amz-checksum-sha1'][0] ?? null;
        $this->checksumSha256 = $headers['x-amz-checksum-sha256'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
    }
}
