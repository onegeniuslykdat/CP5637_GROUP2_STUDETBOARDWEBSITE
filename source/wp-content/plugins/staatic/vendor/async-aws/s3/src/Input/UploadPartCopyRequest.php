<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class UploadPartCopyRequest extends Input
{
    private $bucket;
    private $copySource;
    private $copySourceIfMatch;
    private $copySourceIfModifiedSince;
    private $copySourceIfNoneMatch;
    private $copySourceIfUnmodifiedSince;
    private $copySourceRange;
    private $key;
    private $partNumber;
    private $uploadId;
    private $sseCustomerAlgorithm;
    private $sseCustomerKey;
    private $sseCustomerKeyMd5;
    private $copySourceSseCustomerAlgorithm;
    private $copySourceSseCustomerKey;
    private $copySourceSseCustomerKeyMd5;
    private $requestPayer;
    private $expectedBucketOwner;
    private $expectedSourceBucketOwner;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->copySource = $input['CopySource'] ?? null;
        $this->copySourceIfMatch = $input['CopySourceIfMatch'] ?? null;
        $this->copySourceIfModifiedSince = (!isset($input['CopySourceIfModifiedSince'])) ? null : (($input['CopySourceIfModifiedSince'] instanceof DateTimeImmutable) ? $input['CopySourceIfModifiedSince'] : new DateTimeImmutable($input['CopySourceIfModifiedSince']));
        $this->copySourceIfNoneMatch = $input['CopySourceIfNoneMatch'] ?? null;
        $this->copySourceIfUnmodifiedSince = (!isset($input['CopySourceIfUnmodifiedSince'])) ? null : (($input['CopySourceIfUnmodifiedSince'] instanceof DateTimeImmutable) ? $input['CopySourceIfUnmodifiedSince'] : new DateTimeImmutable($input['CopySourceIfUnmodifiedSince']));
        $this->copySourceRange = $input['CopySourceRange'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->uploadId = $input['UploadId'] ?? null;
        $this->sseCustomerAlgorithm = $input['SSECustomerAlgorithm'] ?? null;
        $this->sseCustomerKey = $input['SSECustomerKey'] ?? null;
        $this->sseCustomerKeyMd5 = $input['SSECustomerKeyMD5'] ?? null;
        $this->copySourceSseCustomerAlgorithm = $input['CopySourceSSECustomerAlgorithm'] ?? null;
        $this->copySourceSseCustomerKey = $input['CopySourceSSECustomerKey'] ?? null;
        $this->copySourceSseCustomerKeyMd5 = $input['CopySourceSSECustomerKeyMD5'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->expectedSourceBucketOwner = $input['ExpectedSourceBucketOwner'] ?? null;
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
    public function getCopySource(): ?string
    {
        return $this->copySource;
    }
    public function getCopySourceIfMatch(): ?string
    {
        return $this->copySourceIfMatch;
    }
    public function getCopySourceIfModifiedSince(): ?DateTimeImmutable
    {
        return $this->copySourceIfModifiedSince;
    }
    public function getCopySourceIfNoneMatch(): ?string
    {
        return $this->copySourceIfNoneMatch;
    }
    public function getCopySourceIfUnmodifiedSince(): ?DateTimeImmutable
    {
        return $this->copySourceIfUnmodifiedSince;
    }
    public function getCopySourceRange(): ?string
    {
        return $this->copySourceRange;
    }
    public function getCopySourceSseCustomerAlgorithm(): ?string
    {
        return $this->copySourceSseCustomerAlgorithm;
    }
    public function getCopySourceSseCustomerKey(): ?string
    {
        return $this->copySourceSseCustomerKey;
    }
    public function getCopySourceSseCustomerKeyMd5(): ?string
    {
        return $this->copySourceSseCustomerKeyMd5;
    }
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }
    public function getExpectedSourceBucketOwner(): ?string
    {
        return $this->expectedSourceBucketOwner;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getPartNumber(): ?int
    {
        return $this->partNumber;
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
        if (null === $v = $this->copySource) {
            throw new InvalidArgument(sprintf('Missing parameter "CopySource" for "%s". The value cannot be null.', __CLASS__));
        }
        $headers['x-amz-copy-source'] = $v;
        if (null !== $this->copySourceIfMatch) {
            $headers['x-amz-copy-source-if-match'] = $this->copySourceIfMatch;
        }
        if (null !== $this->copySourceIfModifiedSince) {
            $headers['x-amz-copy-source-if-modified-since'] = $this->copySourceIfModifiedSince->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->copySourceIfNoneMatch) {
            $headers['x-amz-copy-source-if-none-match'] = $this->copySourceIfNoneMatch;
        }
        if (null !== $this->copySourceIfUnmodifiedSince) {
            $headers['x-amz-copy-source-if-unmodified-since'] = $this->copySourceIfUnmodifiedSince->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->copySourceRange) {
            $headers['x-amz-copy-source-range'] = $this->copySourceRange;
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
        if (null !== $this->copySourceSseCustomerAlgorithm) {
            $headers['x-amz-copy-source-server-side-encryption-customer-algorithm'] = $this->copySourceSseCustomerAlgorithm;
        }
        if (null !== $this->copySourceSseCustomerKey) {
            $headers['x-amz-copy-source-server-side-encryption-customer-key'] = $this->copySourceSseCustomerKey;
        }
        if (null !== $this->copySourceSseCustomerKeyMd5) {
            $headers['x-amz-copy-source-server-side-encryption-customer-key-MD5'] = $this->copySourceSseCustomerKeyMd5;
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
        if (null !== $this->expectedSourceBucketOwner) {
            $headers['x-amz-source-expected-bucket-owner'] = $this->expectedSourceBucketOwner;
        }
        $query = [];
        if (null === $v = $this->partNumber) {
            throw new InvalidArgument(sprintf('Missing parameter "PartNumber" for "%s". The value cannot be null.', __CLASS__));
        }
        $query['partNumber'] = (string) $v;
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
        $body = '';
        return new Request('PUT', $uriString, $query, $headers, StreamFactory::create($body));
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
    public function setCopySource($value): self
    {
        $this->copySource = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceIfMatch($value): self
    {
        $this->copySourceIfMatch = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setCopySourceIfModifiedSince($value): self
    {
        $this->copySourceIfModifiedSince = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceIfNoneMatch($value): self
    {
        $this->copySourceIfNoneMatch = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setCopySourceIfUnmodifiedSince($value): self
    {
        $this->copySourceIfUnmodifiedSince = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceRange($value): self
    {
        $this->copySourceRange = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceSseCustomerAlgorithm($value): self
    {
        $this->copySourceSseCustomerAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceSseCustomerKey($value): self
    {
        $this->copySourceSseCustomerKey = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCopySourceSseCustomerKeyMd5($value): self
    {
        $this->copySourceSseCustomerKeyMd5 = $value;
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
    public function setExpectedSourceBucketOwner($value): self
    {
        $this->expectedSourceBucketOwner = $value;
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
     * @param int|null $value
     */
    public function setPartNumber($value): self
    {
        $this->partNumber = $value;
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
}
