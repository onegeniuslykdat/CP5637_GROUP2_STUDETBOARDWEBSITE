<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Delete
{
    private $objects;
    private $quiet;
    public function __construct(array $input)
    {
        $this->objects = isset($input['Objects']) ? array_map([ObjectIdentifier::class, 'create'], $input['Objects']) : $this->throwException(new InvalidArgument('Missing required field "Objects".'));
        $this->quiet = $input['Quiet'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getObjects(): array
    {
        return $this->objects;
    }
    public function getQuiet(): ?bool
    {
        return $this->quiet;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        $v = $this->objects;
        foreach ($v as $item) {
            $node->appendChild($child = $document->createElement('Object'));
            $item->requestBody($child, $document);
        }
        if (null !== $v = $this->quiet) {
            $node->appendChild($document->createElement('Quiet', $v ? 'true' : 'false'));
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
