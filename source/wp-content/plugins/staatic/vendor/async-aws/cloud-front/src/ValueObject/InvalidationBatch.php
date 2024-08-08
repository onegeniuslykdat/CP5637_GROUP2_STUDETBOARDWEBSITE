<?php

namespace Staatic\Vendor\AsyncAws\CloudFront\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class InvalidationBatch
{
    private $paths;
    private $callerReference;
    public function __construct(array $input)
    {
        $this->paths = isset($input['Paths']) ? Paths::create($input['Paths']) : $this->throwException(new InvalidArgument('Missing required field "Paths".'));
        $this->callerReference = $input['CallerReference'] ?? $this->throwException(new InvalidArgument('Missing required field "CallerReference".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getCallerReference(): string
    {
        return $this->callerReference;
    }
    public function getPaths(): Paths
    {
        return $this->paths;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        $v = $this->paths;
        $node->appendChild($child = $document->createElement('Paths'));
        $v->requestBody($child, $document);
        $v = $this->callerReference;
        $node->appendChild($document->createElement('CallerReference', $v));
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
