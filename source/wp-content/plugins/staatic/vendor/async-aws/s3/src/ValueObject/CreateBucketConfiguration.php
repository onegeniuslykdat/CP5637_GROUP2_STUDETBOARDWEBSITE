<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\BucketLocationConstraint;
final class CreateBucketConfiguration
{
    private $locationConstraint;
    private $location;
    private $bucket;
    public function __construct(array $input)
    {
        $this->locationConstraint = $input['LocationConstraint'] ?? null;
        $this->location = isset($input['Location']) ? LocationInfo::create($input['Location']) : null;
        $this->bucket = isset($input['Bucket']) ? BucketInfo::create($input['Bucket']) : null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getBucket(): ?BucketInfo
    {
        return $this->bucket;
    }
    public function getLocation(): ?LocationInfo
    {
        return $this->location;
    }
    public function getLocationConstraint(): ?string
    {
        return $this->locationConstraint;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->locationConstraint) {
            if (!BucketLocationConstraint::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "LocationConstraint" for "%s". The value "%s" is not a valid "BucketLocationConstraint".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('LocationConstraint', $v));
        }
        if (null !== $v = $this->location) {
            $node->appendChild($child = $document->createElement('Location'));
            $v->requestBody($child, $document);
        }
        if (null !== $v = $this->bucket) {
            $node->appendChild($child = $document->createElement('Bucket'));
            $v->requestBody($child, $document);
        }
    }
}
