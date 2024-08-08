<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class Owner
{
    private $displayName;
    private $id;
    public function __construct(array $input)
    {
        $this->displayName = $input['DisplayName'] ?? null;
        $this->id = $input['ID'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }
    public function getId(): ?string
    {
        return $this->id;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->displayName) {
            $node->appendChild($document->createElement('DisplayName', $v));
        }
        if (null !== $v = $this->id) {
            $node->appendChild($document->createElement('ID', $v));
        }
    }
}
