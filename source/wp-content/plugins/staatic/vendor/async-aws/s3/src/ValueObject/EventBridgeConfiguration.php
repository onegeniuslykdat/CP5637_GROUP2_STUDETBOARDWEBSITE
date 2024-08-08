<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\S3\ValueObject\EventBridgeConfiguration as EventBridgeConfiguration1;
final class EventBridgeConfiguration
{
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self();
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
    }
}
