<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CompletedMultipartUpload;
final class CompleteMultipartUploadRequest extends Input
{
    private $bucket;
    private $key;
    private $multipartUpload;
    private $uploadId;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $requestPayer;
    private $expectedBucketOwner;
    private $sseCustomerAlgorithm;
    private $sseCustomerKey;
    private $sseCustomerKeyMd5;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->multipartUpload = isset($input['MultipartUpload']) ? CompletedMultipartUpload::create($input['MultipartUpload']) : null;
        $this->uploadId = $input['UploadId'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->sseCustomerAlgorithm = $input['SSECustomerAlgorithm'] ?? null;
        $this->sseCustomerKey = $input['SSECustomerKey'] ?? null;
        $this->sseCustomerKeyMd5 = $input['SSECustomerKeyMD5'] ?? null;
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getBucket(): ?string
    {
        return $this->bucket;
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
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getMultipartUpload(): ?CompletedMultipartUpload
    {
        return $this->multipartUpload;
    }
    public function getRequestPayer(): ?string
    {
        return $this->requestPayer;
    }
    public function getSseCustomerAlgorithm(): ?string
    {
        return $this->sseCustomerAlgorithm;
    }
    public function getSseCustomerKey(): ?string
    {
        return $this->sseCustomerKey;
    }
    public function getSseCustomerKeyMd5(): ?string
    {
        return $this->sseCustomerKeyMd5;
    }
    public function getUploadId(): ?string
    {
        return $this->uploadId;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->checksumCrc32) {
            $headers['x-amz-checksum-crc32'] = $this->checksumCrc32;
        }
        if (null !== $this->checksumCrc32C) {
            $headers['x-amz-checksum-crc32c'] = $this->checksumCrc32C;
        }
        if (null !== $this->checksumSha1) {
            $headers['x-amz-checksum-sha1'] = $this->checksumSha1;
        }
        if (null !== $this->checksumSha256) {
            $headers['x-amz-checksum-sha256'] = $this->checksumSha256;
        }
        if (null !== $this->requestPayer) {
            if (!RequestPayer::exists($this->requestPayer)) {
                throw new InvalidArgument(sprintf('Invalid parameter "RequestPayer" for "%s". The value "%s" is not a valid "RequestPayer".', __CLASS__, $this->requestPayer));
            }
            $headers['x-amz-request-payer'] = $this->requestPayer;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        if (null !== $this->sseCustomerAlgorithm) {
            $headers['x-amz-server-side-encryption-customer-algorithm'] = $this->sseCustomerAlgorithm;
        }
        if (null !== $this->sseCustomerKey) {
            $headers['x-amz-server-side-encryption-customer-key'] = $this->sseCustomerKey;
        }
        if (null !== $this->sseCustomerKeyMd5) {
            $headers['x-amz-server-side-encryption-customer-key-MD5'] = $this->sseCustomerKeyMd5;
        }
        $query = [];
        if (null === $v = $this->uploadId) {
            throw new InvalidArgument(sprintf('Missing parameter "UploadId" for "%s". The value cannot be null.', __CLASS__));
        }
        $query['uploadId'] = $v;
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        if (null === $v = $this->key) {
            throw new InvalidArgument(sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Key'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '/' . str_replace('%2F', '/', rawurlencode($uri['Key']));
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';
        return new Request('POST', $uriString, $query, $headers, StreamFactory::create($body));
    }
    /**
     * @param string|null $value
     */
    public function setBucket($value): self
    {
        $this->bucket = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumCrc32($value): self
    {
        $this->checksumCrc32 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumCrc32C($value): self
    {
        $this->checksumCrc32C = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumSha1($value): self
    {
        $this->checksumSha1 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumSha256($value): self
    {
        $this->checksumSha256 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setExpectedBucketOwner($value): self
    {
        $this->expectedBucketOwner = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setKey($value): self
    {
        $this->key = $value;
        return $this;
    }
    /**
     * @param CompletedMultipartUpload|null $value
     */
    public function setMultipartUpload($value): self
    {
        $this->multipartUpload = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setRequestPayer($value): self
    {
        $this->requestPayer = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerAlgorithm($value): self
    {
        $this->sseCustomerAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerKey($value): self
    {
        $this->sseCustomerKey = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerKeyMd5($value): self
    {
        $this->sseCustomerKeyMd5 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setUploadId($value): self
    {
        $this->uploadId = $value;
        return $this;
    }
    private function requestBody(DOMNode $node, DOMDocument $document): void
    {
        if (null !== $v = $this->multipartUpload) {
            $node->appendChild($child = $document->createElement('CompleteMultipartUpload'));
            $child->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
            $v->requestBody($child, $document);
        }
    }
}
