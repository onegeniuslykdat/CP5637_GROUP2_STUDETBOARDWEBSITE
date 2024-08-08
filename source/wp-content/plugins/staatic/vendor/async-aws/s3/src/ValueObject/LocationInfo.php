<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\LocationType;
final class LocationInfo
{
    private $type;
    private $name;
    public function __construct(array $input)
    {
        $this->type = $input['Type'] ?? null;
        $this->name = $input['Name'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->type) {
            if (!LocationType::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "Type" for "%s". The value "%s" is not a valid "LocationType".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('Type', $v));
        }
        if (null !== $v = $this->name) {
            $node->appendChild($document->createElement('Name', $v));
        }
    }
}
