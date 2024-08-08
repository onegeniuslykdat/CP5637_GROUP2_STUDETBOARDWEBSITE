<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use DateTimeImmutable;
use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
class CreateMultipartUploadOutput extends Result
{
    private $abortDate;
    private $abortRuleId;
    private $bucket;
    private $key;
    private $uploadId;
    private $serverSideEncryption;
    private $sseCustomerAlgorithm;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $sseKmsEncryptionContext;
    private $bucketKeyEnabled;
    private $requestCharged;
    private $checksumAlgorithm;
    public function getAbortDate(): ?DateTimeImmutable
    {
        $this->initialize();
        return $this->abortDate;
    }
    public function getAbortRuleId(): ?string
    {
        $this->initialize();
        return $this->abortRuleId;
    }
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
    public function getChecksumAlgorithm(): ?string
    {
        $this->initialize();
        return $this->checksumAlgorithm;
    }
    public function getKey(): ?string
    {
        $this->initialize();
        return $this->key;
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
    public function getSseKmsEncryptionContext(): ?string
    {
        $this->initialize();
        return $this->sseKmsEncryptionContext;
    }
    public function getSseKmsKeyId(): ?string
    {
        $this->initialize();
        return $this->sseKmsKeyId;
    }
    public function getUploadId(): ?string
    {
        $this->initialize();
        return $this->uploadId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->abortDate = isset($headers['x-amz-abort-date'][0]) ? new DateTimeImmutable($headers['x-amz-abort-date'][0]) : null;
        $this->abortRuleId = $headers['x-amz-abort-rule-id'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->sseKmsEncryptionContext = $headers['x-amz-server-side-encryption-context'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $this->checksumAlgorithm = $headers['x-amz-checksum-algorithm'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->uploadId = ($v = $data->UploadId) ? (string) $v : null;
    }
}
