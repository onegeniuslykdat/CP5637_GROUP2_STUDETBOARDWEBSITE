<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class CompletedMultipartUpload
{
    private $parts;
    public function __construct(array $input)
    {
        $this->parts = isset($input['Parts']) ? array_map([CompletedPart::class, 'create'], $input['Parts']) : null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getParts(): array
    {
        return $this->parts ?? [];
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->parts) {
            foreach ($v as $item) {
                $node->appendChild($child = $document->createElement('Part'));
                $item->requestBody($child, $document);
            }
        }
    }
}
