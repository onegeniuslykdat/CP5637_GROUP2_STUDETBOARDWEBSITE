<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\BucketCannedACL;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectOwnership;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CreateBucketConfiguration;
final class CreateBucketRequest extends Input
{
    private $acl;
    private $bucket;
    private $createBucketConfiguration;
    private $grantFullControl;
    private $grantRead;
    private $grantReadAcp;
    private $grantWrite;
    private $grantWriteAcp;
    private $objectLockEnabledForBucket;
    private $objectOwnership;
    public function __construct(array $input = [])
    {
        $this->acl = $input['ACL'] ?? null;
        $this->bucket = $input['Bucket'] ?? null;
        $this->createBucketConfiguration = isset($input['CreateBucketConfiguration']) ? CreateBucketConfiguration::create($input['CreateBucketConfiguration']) : null;
        $this->grantFullControl = $input['GrantFullControl'] ?? null;
        $this->grantRead = $input['GrantRead'] ?? null;
        $this->grantReadAcp = $input['GrantReadACP'] ?? null;
        $this->grantWrite = $input['GrantWrite'] ?? null;
        $this->grantWriteAcp = $input['GrantWriteACP'] ?? null;
        $this->objectLockEnabledForBucket = $input['ObjectLockEnabledForBucket'] ?? null;
        $this->objectOwnership = $input['ObjectOwnership'] ?? null;
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
    public function getCreateBucketConfiguration(): ?CreateBucketConfiguration
    {
        return $this->createBucketConfiguration;
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
    public function getGrantWrite(): ?string
    {
        return $this->grantWrite;
    }
    public function getGrantWriteAcp(): ?string
    {
        return $this->grantWriteAcp;
    }
    public function getObjectLockEnabledForBucket(): ?bool
    {
        return $this->objectLockEnabledForBucket;
    }
    public function getObjectOwnership(): ?string
    {
        return $this->objectOwnership;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->acl) {
            if (!BucketCannedACL::exists($this->acl)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ACL" for "%s". The value "%s" is not a valid "BucketCannedACL".', __CLASS__, $this->acl));
            }
            $headers['x-amz-acl'] = $this->acl;
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
        if (null !== $this->grantWrite) {
            $headers['x-amz-grant-write'] = $this->grantWrite;
        }
        if (null !== $this->grantWriteAcp) {
            $headers['x-amz-grant-write-acp'] = $this->grantWriteAcp;
        }
        if (null !== $this->objectLockEnabledForBucket) {
            $headers['x-amz-bucket-object-lock-enabled'] = $this->objectLockEnabledForBucket ? 'true' : 'false';
        }
        if (null !== $this->objectOwnership) {
            if (!ObjectOwnership::exists($this->objectOwnership)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ObjectOwnership" for "%s". The value "%s" is not a valid "ObjectOwnership".', __CLASS__, $this->objectOwnership));
            }
            $headers['x-amz-object-ownership'] = $this->objectOwnership;
        }
        $query = [];
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']);
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';
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
     * @param CreateBucketConfiguration|null $value
     */
    public function setCreateBucketConfiguration($value): self
    {
        $this->createBucketConfiguration = $value;
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
    public function setGrantWrite($value): self
    {
        $this->grantWrite = $value;
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
     * @param bool|null $value
     */
    public function setObjectLockEnabledForBucket($value): self
    {
        $this->objectLockEnabledForBucket = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setObjectOwnership($value): self
    {
        $this->objectOwnership = $value;
        return $this;
    }
    private function requestBody(DOMNode $node, DOMDocument $document): void
    {
        if (null !== $v = $this->createBucketConfiguration) {
            $node->appendChild($child = $document->createElement('CreateBucketConfiguration'));
            $child->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
            $v->requestBody($child, $document);
        }
    }
}
