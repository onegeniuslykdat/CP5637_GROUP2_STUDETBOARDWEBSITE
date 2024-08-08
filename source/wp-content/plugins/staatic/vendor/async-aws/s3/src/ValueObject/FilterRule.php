<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\FilterRuleName;
final class FilterRule
{
    private $name;
    private $value;
    public function __construct(array $input)
    {
        $this->name = $input['Name'] ?? null;
        $this->value = $input['Value'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getValue(): ?string
    {
        return $this->value;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->name) {
            if (!FilterRuleName::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "Name" for "%s". The value "%s" is not a valid "FilterRuleName".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('Name', $v));
        }
        if (null !== $v = $this->value) {
            $node->appendChild($document->createElement('Value', $v));
        }
    }
}
