<?php

namespace Staatic\Vendor\AsyncAws\CloudFront\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Paths
{
    private $quantity;
    private $items;
    public function __construct(array $input)
    {
        $this->quantity = $input['Quantity'] ?? $this->throwException(new InvalidArgument('Missing required field "Quantity".'));
        $this->items = $input['Items'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getItems(): array
    {
        return $this->items ?? [];
    }
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        $v = $this->quantity;
        $node->appendChild($document->createElement('Quantity', (string) $v));
        if (null !== $v = $this->items) {
            $node->appendChild($nodeList = $document->createElement('Items'));
            foreach ($v as $item) {
                $nodeList->appendChild($document->createElement('Path', $item));
            }
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
