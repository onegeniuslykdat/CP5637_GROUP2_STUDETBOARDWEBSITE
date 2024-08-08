<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CORSConfiguration;
final class PutBucketCorsRequest extends Input
{
    private $bucket;
    private $corsConfiguration;
    private $contentMd5;
    private $checksumAlgorithm;
    private $expectedBucketOwner;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->corsConfiguration = isset($input['CORSConfiguration']) ? CORSConfiguration::create($input['CORSConfiguration']) : null;
        $this->contentMd5 = $input['ContentMD5'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
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
    public function getCorsConfiguration(): ?CORSConfiguration
    {
        return $this->corsConfiguration;
    }
    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
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
        $query = [];
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '?cors';
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
     * @param CORSConfiguration|null $value
     */
    public function setCorsConfiguration($value): self
    {
        $this->corsConfiguration = $value;
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
    private function requestBody(DOMNode $node, DOMDocument $document): void
    {
        if (null === $v = $this->corsConfiguration) {
            throw new InvalidArgument(sprintf('Missing parameter "CORSConfiguration" for "%s". The value cannot be null.', __CLASS__));
        }
        $node->appendChild($child = $document->createElement('CORSConfiguration'));
        $child->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
        $v->requestBody($child, $document);
    }
}
