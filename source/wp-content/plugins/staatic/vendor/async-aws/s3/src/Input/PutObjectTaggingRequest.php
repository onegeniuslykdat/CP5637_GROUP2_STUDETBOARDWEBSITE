<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Tag;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Tagging;
final class PutObjectTaggingRequest extends Input
{
    private $bucket;
    private $key;
    private $versionId;
    private $contentMd5;
    private $checksumAlgorithm;
    private $tagging;
    private $expectedBucketOwner;
    private $requestPayer;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->contentMd5 = $input['ContentMD5'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->tagging = isset($input['Tagging']) ? Tagging::create($input['Tagging']) : null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
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
    public function getChecksumAlgorithm(): ?string
    {
        return $this->checksumAlgorithm;
    }
    public function getContentMd5(): ?string
    {
        return $this->contentMd5;
    }
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getRequestPayer(): ?string
    {
        return $this->requestPayer;
    }
    public function getTagging(): ?Tagging
    {
        return $this->tagging;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->contentMd5) {
            $headers['Content-MD5'] = $this->contentMd5;
        }
        if (null !== $this->checksumAlgorithm) {
            if (!ChecksumAlgorithm::exists($this->checksumAlgorithm)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ChecksumAlgorithm" for "%s". The value "%s" is not a valid "ChecksumAlgorithm".', __CLASS__, $this->checksumAlgorithm));
            }
            $headers['x-amz-sdk-checksum-algorithm'] = $this->checksumAlgorithm;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        if (null !== $this->requestPayer) {
            if (!RequestPayer::exists($this->requestPayer)) {
                throw new InvalidArgument(sprintf('Invalid parameter "RequestPayer" for "%s". The value "%s" is not a valid "RequestPayer".', __CLASS__, $this->requestPayer));
            }
            $headers['x-amz-request-payer'] = $this->requestPayer;
        }
        $query = [];
        if (null !== $this->versionId) {
            $query['versionId'] = $this->versionId;
        }
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        if (null === $v = $this->key) {
            throw new InvalidArgument(sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Key'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '/' . str_replace('%2F', '/', rawurlencode($uri['Key'])) . '?tagging';
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';
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
    public function setChecksumAlgorithm($value): self
    {
        $this->checksumAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentMd5($value): self
    {
        $this->contentMd5 = $value;
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
     * @param string|null $value
     */
    public function setRequestPayer($value): self
    {
        $this->requestPayer = $value;
        return $this;
    }
    /**
     * @param Tagging|null $value
     */
    public function setTagging($value): self
    {
        $this->tagging = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setVersionId($value): self
    {
        $this->versionId = $value;
        return $this;
    }
    private function requestBody(DOMNode $node, DOMDocument $document): void
    {
        if (null === $v = $this->tagging) {
            throw new InvalidArgument(sprintf('Missing parameter "Tagging" for "%s". The value cannot be null.', __CLASS__));
        }
        $node->appendChild($child = $document->createElement('Tagging'));
        $child->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
        $v->requestBody($child, $document);
    }
}
