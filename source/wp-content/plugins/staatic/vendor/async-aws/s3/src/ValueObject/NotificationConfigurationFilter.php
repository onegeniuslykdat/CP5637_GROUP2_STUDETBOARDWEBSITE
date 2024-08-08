<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class NotificationConfigurationFilter
{
    private $key;
    public function __construct(array $input)
    {
        $this->key = isset($input['Key']) ? S3KeyFilter::create($input['Key']) : null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getKey(): ?S3KeyFilter
    {
        return $this->key;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->key) {
            $node->appendChild($child = $document->createElement('S3Key'));
            $v->requestBody($child, $document);
        }
    }
}
