<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\MetadataDirective;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectCannedACL;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockLegalHoldStatus;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockMode;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
use Staatic\Vendor\AsyncAws\S3\Enum\TaggingDirective;
final class CopyObjectRequest extends Input
{
    private $acl;
    private $bucket;
    private $cacheControl;
    private $checksumAlgorithm;
    private $contentDisposition;
    private $contentEncoding;
    private $contentLanguage;
    private $contentType;
    private $copySource;
    private $copySourceIfMatch;
    private $copySourceIfModifiedSince;
    private $copySourceIfNoneMatch;
    private $copySourceIfUnmodifiedSince;
    private $expires;
    private $grantFullControl;
    private $grantRead;
    private $grantReadAcp;
    private $grantWriteAcp;
    private $key;
    private $metadata;
    private $metadataDirective;
    private $taggingDirective;
    private $serverSideEncryption;
    private $storageClass;
    private $websiteRedirectLocation;
    private $sseCustomerAlgorithm;
    private $sseCustomerKey;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $sseKmsEncryptionContext;
    private $bucketKeyEnabled;
    private $copySourceSseCustomerAlgorithm;
    private $copySourceSseCustomerKey;
    private $copySourceSseCustomerKeyMd5;
    private $requestPayer;
    private $tagging;
    private $objectLockMode;
    private $objectLockRetainUntilDate;
    private $objectLockLegalHoldStatus;
    private $expectedBucketOwner;
    private $expectedSourceBucketOwner;
    public function __construct(array $input = [])
    {
        $this->acl = $input['ACL'] ?? null;
        $this->bucket = $input['Bucket'] ?? null;
        $this->cacheControl = $input['CacheControl'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->contentDisposition = $input['ContentDisposition'] ?? null;
        $this->contentEncoding = $input['ContentEncoding'] ?? null;
        $this->contentLanguage = $input['ContentLanguage'] ?? null;
        $this->contentType = $input['ContentType'] ?? null;
        $this->copySource = $input['CopySource'] ?? null;
        $this->copySourceIfMatch = $input['CopySourceIfMatch'] ?? null;
        $this->copySourceIfModifiedSince = (!isset($input['CopySourceIfModifiedSince'])) ? null : (($input['CopySourceIfModifiedSince'] instanceof DateTimeImmutable) ? $input['CopySourceIfModifiedSince'] : new DateTimeImmutable($input['CopySourceIfModifiedSince']));
        $this->copySourceIfNoneMatch = $input['CopySourceIfNoneMatch'] ?? null;
        $this->copySourceIfUnmodifiedSince = (!isset($input['CopySourceIfUnmodifiedSince'])) ? null : (($input['CopySourceIfUnmodifiedSince'] instanceof DateTimeImmutable) ? $input['CopySourceIfUnmodifiedSince'] : new DateTimeImmutable($input['CopySourceIfUnmodifiedSince']));
        $this->expires = (!isset($input['Expires'])) ? null : (($input['Expires'] instanceof DateTimeImmutable) ? $input['Expires'] : new DateTimeImmutable($input['Expires']));
        $this->grantFullControl = $input['GrantFullControl'] ?? null;
        $this->grantRead = $input['GrantRead'] ?? null;
        $this->grantReadAcp = $input['GrantReadACP'] ?? null;
        $this->grantWriteAcp = $input['GrantWriteACP'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->metadata = $input['Metadata'] ?? null;
        $this->metadataDirective = $input['MetadataDirective'] ?? null;
        $this->taggingDirective = $input['TaggingDirective'] ?? null;
        $this->serverSideEncryption = $input['ServerSideEncryption'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->websiteRedirectLocation = $input['WebsiteRedirectLocation'] ?? null;
        $this->sseCustomerAlgorithm = $input['SSECustomerAlgorithm'] ?? null;
        $this->sseCustomerKey = $input['SSECustomerKey'] ?? null;
        $this->sseCustomerKeyMd5 = $input['SSECustomerKeyMD5'] ?? null;
        $this->sseKmsKeyId = $input['SSEKMSKeyId'] ?? null;
        $this->sseKmsEncryptionContext = $input['SSEKMSEncryptionContext'] ?? null;
        $this->bucketKeyEnabled = $input['BucketKeyEnabled'] ?? null;
        $this->copySourceSseCustomerAlgorithm = $input['CopySourceSSECustomerAlgorithm'] ?? null;
        $this->copySourceSseCustomerKey = $input['CopySourceSSECustomerKey'] ?? null;
        $this->copySourceSseCustomerKeyMd5 = $input['CopySourceSSECustomerKeyMD5'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->tagging = $input['Tagging'] ?? null;
        $this->objectLockMode = $input['ObjectLockMode'] ?? null;
        $this->objectLockRetainUntilDate = (!isset($input['ObjectLockRetainUntilDate'])) ? null : (($input['ObjectLockRetainUntilDate'] instanceof DateTimeImmutable) ? $input['ObjectLockRetainUntilDate'] : new DateTimeImmutable($input['ObjectLockRetainUntilDate']));
        $this->objectLockLegalHoldStatus = $input['ObjectLockLegalHoldStatus'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->expectedSourceBucketOwner = $input['ExpectedSourceBucketOwner'] ?? null;
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getAcl(): ?string
    {
        return $this->acl;
    }
    public function getBucket(): ?string
    {
        return $this->bucket;
    }
    public function getBucketKeyEnabled(): ?bool
    {
        return $this->bucketKeyEnabled;
    }
    public function getCacheControl(): ?string
    {
        return $this->cacheControl;
    }
    public function getChecksumAlgorithm(): ?string
    {
        return $this->checksumAlgorithm;
    }
    public function getContentDisposition(): ?string
    {
        return $this->contentDisposition;
    }
    public function getContentEncoding(): ?string
    {
        return $this->contentEncoding;
    }
    public function getContentLanguage(): ?string
    {
        return $this->contentLanguage;
    }
    public function getContentType(): ?string
    {
        return $this->contentType;
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
    public function getExpires(): ?DateTimeImmutable
    {
        return $this->expires;
    }
    public function getGrantFullControl(): ?string
    {
        return $this->grantFullControl;
    }
    public function getGrantRead(): ?string
    {
        return $this->grantRead;
    }
    public function getGrantReadAcp(): ?string
    {
        return $this->grantReadAcp;
    }
    public function getGrantWriteAcp(): ?string
    {
        return $this->grantWriteAcp;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }
    public function getMetadataDirective(): ?string
    {
        return $this->metadataDirective;
    }
    public function getObjectLockLegalHoldStatus(): ?string
    {
        return $this->objectLockLegalHoldStatus;
    }
    public function getObjectLockMode(): ?string
    {
        return $this->objectLockMode;
    }
    public function getObjectLockRetainUntilDate(): ?DateTimeImmutable
    {
        return $this->objectLockRetainUntilDate;
    }
    public function getRequestPayer(): ?string
    {
        return $this->requestPayer;
    }
    public function getServerSideEncryption(): ?string
    {
        return $this->serverSideEncryption;
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
    public function getSseKmsEncryptionContext(): ?string
    {
        return $this->sseKmsEncryptionContext;
    }
    public function getSseKmsKeyId(): ?string
    {
        return $this->sseKmsKeyId;
    }
    public function getStorageClass(): ?string
    {
        return $this->storageClass;
    }
    public function getTagging(): ?string
    {
        return $this->tagging;
    }
    public function getTaggingDirective(): ?string
    {
        return $this->taggingDirective;
    }
    public function getWebsiteRedirectLocation(): ?string
    {
        return $this->websiteRedirectLocation;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->acl) {
            if (!ObjectCannedACL::exists($this->acl)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ACL" for "%s". The value "%s" is not a valid "ObjectCannedACL".', __CLASS__, $this->acl));
            }
            $headers['x-amz-acl'] = $this->acl;
        }
        if (null !== $this->cacheControl) {
            $headers['Cache-Control'] = $this->cacheControl;
        }
        if (null !== $this->checksumAlgorithm) {
            if (!ChecksumAlgorithm::exists($this->checksumAlgorithm)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ChecksumAlgorithm" for "%s". The value "%s" is not a valid "ChecksumAlgorithm".', __CLASS__, $this->checksumAlgorithm));
            }
            $headers['x-amz-checksum-algorithm'] = $this->checksumAlgorithm;
        }
        if (null !== $this->contentDisposition) {
            $headers['Content-Disposition'] = $this->contentDisposition;
        }
        if (null !== $this->contentEncoding) {
            $headers['Content-Encoding'] = $this->contentEncoding;
        }
        if (null !== $this->contentLanguage) {
            $headers['Content-Language'] = $this->contentLanguage;
        }
        if (null !== $this->contentType) {
            $headers['Content-Type'] = $this->contentType;
        }
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
        if (null !== $this->expires) {
            $headers['Expires'] = $this->expires->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->grantFullControl) {
            $headers['x-amz-grant-full-control'] = $this->grantFullControl;
        }
        if (null !== $this->grantRead) {
            $headers['x-amz-grant-read'] = $this->grantRead;
        }
        if (null !== $this->grantReadAcp) {
            $headers['x-amz-grant-read-acp'] = $this->grantReadAcp;
        }
        if (null !== $this->grantWriteAcp) {
            $headers['x-amz-grant-write-acp'] = $this->grantWriteAcp;
        }
        if (null !== $this->metadataDirective) {
            if (!MetadataDirective::exists($this->metadataDirective)) {
                throw new InvalidArgument(sprintf('Invalid parameter "MetadataDirective" for "%s". The value "%s" is not a valid "MetadataDirective".', __CLASS__, $this->metadataDirective));
            }
            $headers['x-amz-metadata-directive'] = $this->metadataDirective;
        }
        if (null !== $this->taggingDirective) {
            if (!TaggingDirective::exists($this->taggingDirective)) {
                throw new InvalidArgument(sprintf('Invalid parameter "TaggingDirective" for "%s". The value "%s" is not a valid "TaggingDirective".', __CLASS__, $this->taggingDirective));
            }
            $headers['x-amz-tagging-directive'] = $this->taggingDirective;
        }
        if (null !== $this->serverSideEncryption) {
            if (!ServerSideEncryption::exists($this->serverSideEncryption)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ServerSideEncryption" for "%s". The value "%s" is not a valid "ServerSideEncryption".', __CLASS__, $this->serverSideEncryption));
            }
            $headers['x-amz-server-side-encryption'] = $this->serverSideEncryption;
        }
        if (null !== $this->storageClass) {
            if (!StorageClass::exists($this->storageClass)) {
                throw new InvalidArgument(sprintf('Invalid parameter "StorageClass" for "%s". The value "%s" is not a valid "StorageClass".', __CLASS__, $this->storageClass));
            }
            $headers['x-amz-storage-class'] = $this->storageClass;
        }
        if (null !== $this->websiteRedirectLocation) {
            $headers['x-amz-website-redirect-location'] = $this->websiteRedirectLocation;
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
        if (null !== $this->sseKmsKeyId) {
            $headers['x-amz-server-side-encryption-aws-kms-key-id'] = $this->sseKmsKeyId;
        }
        if (null !== $this->sseKmsEncryptionContext) {
            $headers['x-amz-server-side-encryption-context'] = $this->sseKmsEncryptionContext;
        }
        if (null !== $this->bucketKeyEnabled) {
            $headers['x-amz-server-side-encryption-bucket-key-enabled'] = $this->bucketKeyEnabled ? 'true' : 'false';
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
        if (null !== $this->tagging) {
            $headers['x-amz-tagging'] = $this->tagging;
        }
        if (null !== $this->objectLockMode) {
            if (!ObjectLockMode::exists($this->objectLockMode)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ObjectLockMode" for "%s". The value "%s" is not a valid "ObjectLockMode".', __CLASS__, $this->objectLockMode));
            }
            $headers['x-amz-object-lock-mode'] = $this->objectLockMode;
        }
        if (null !== $this->objectLockRetainUntilDate) {
            $headers['x-amz-object-lock-retain-until-date'] = $this->objectLockRetainUntilDate->format(DateTimeInterface::ATOM);
        }
        if (null !== $this->objectLockLegalHoldStatus) {
            if (!ObjectLockLegalHoldStatus::exists($this->objectLockLegalHoldStatus)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ObjectLockLegalHoldStatus" for "%s". The value "%s" is not a valid "ObjectLockLegalHoldStatus".', __CLASS__, $this->objectLockLegalHoldStatus));
            }
            $headers['x-amz-object-lock-legal-hold'] = $this->objectLockLegalHoldStatus;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        if (null !== $this->expectedSourceBucketOwner) {
            $headers['x-amz-source-expected-bucket-owner'] = $this->expectedSourceBucketOwner;
        }
        if (null !== $this->metadata) {
            foreach ($this->metadata as $key => $value) {
                $headers["x-amz-meta-{$key}"] = $value;
            }
        }
        $query = [];
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
    public function setAcl($value): self
    {
        $this->acl = $value;
        return $this;
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
     * @param bool|null $value
     */
    public function setBucketKeyEnabled($value): self
    {
        $this->bucketKeyEnabled = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setCacheControl($value): self
    {
        $this->cacheControl = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumAlgorithm($value): self
    {
        $this->checksumAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentDisposition($value): self
    {
        $this->contentDisposition = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentEncoding($value): self
    {
        $this->contentEncoding = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentLanguage($value): self
    {
        $this->contentLanguage = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentType($value): self
    {
        $this->contentType = $value;
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
     * @param DateTimeImmutable|null $value
     */
    public function setExpires($value): self
    {
        $this->expires = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantFullControl($value): self
    {
        $this->grantFullControl = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantRead($value): self
    {
        $this->grantRead = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantReadAcp($value): self
    {
        $this->grantReadAcp = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantWriteAcp($value): self
    {
        $this->grantWriteAcp = $value;
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
     * @param mixed[] $value
     */
    public function setMetadata($value): self
    {
        $this->metadata = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setMetadataDirective($value): self
    {
        $this->metadataDirective = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setObjectLockLegalHoldStatus($value): self
    {
        $this->objectLockLegalHoldStatus = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setObjectLockMode($value): self
    {
        $this->objectLockMode = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setObjectLockRetainUntilDate($value): self
    {
        $this->objectLockRetainUntilDate = $value;
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
    public function setServerSideEncryption($value): self
    {
        $this->serverSideEncryption = $value;
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
    public function setSseKmsEncryptionContext($value): self
    {
        $this->sseKmsEncryptionContext = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseKmsKeyId($value): self
    {
        $this->sseKmsKeyId = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setStorageClass($value): self
    {
        $this->storageClass = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setTagging($value): self
    {
        $this->tagging = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setTaggingDirective($value): self
    {
        $this->taggingDirective = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setWebsiteRedirectLocation($value): self
    {
        $this->websiteRedirectLocation = $value;
        return $this;
    }
}
