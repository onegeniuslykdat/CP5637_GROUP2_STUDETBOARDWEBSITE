<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class ObjectIdentifier
{
    private $key;
    private $versionId;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? $this->throwException(new InvalidArgument('Missing required field "Key".'));
        $this->versionId = $input['VersionId'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        $v = $this->key;
        $node->appendChild($document->createElement('Key', $v));
        if (null !== $v = $this->versionId) {
            $node->appendChild($document->createElement('VersionId', $v));
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
