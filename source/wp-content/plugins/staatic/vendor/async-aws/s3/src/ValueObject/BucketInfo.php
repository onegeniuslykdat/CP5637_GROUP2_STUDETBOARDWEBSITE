<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\BucketType;
use Staatic\Vendor\AsyncAws\S3\Enum\DataRedundancy;
final class BucketInfo
{
    private $dataRedundancy;
    private $type;
    public function __construct(array $input)
    {
        $this->dataRedundancy = $input['DataRedundancy'] ?? null;
        $this->type = $input['Type'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getDataRedundancy(): ?string
    {
        return $this->dataRedundancy;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->dataRedundancy) {
            if (!DataRedundancy::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "DataRedundancy" for "%s". The value "%s" is not a valid "DataRedundancy".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('DataRedundancy', $v));
        }
        if (null !== $v = $this->type) {
            if (!BucketType::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "Type" for "%s". The value "%s" is not a valid "BucketType".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('Type', $v));
        }
    }
}
