<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class CORSRule
{
    private $id;
    private $allowedHeaders;
    private $allowedMethods;
    private $allowedOrigins;
    private $exposeHeaders;
    private $maxAgeSeconds;
    public function __construct(array $input)
    {
        $this->id = $input['ID'] ?? null;
        $this->allowedHeaders = $input['AllowedHeaders'] ?? null;
        $this->allowedMethods = $input['AllowedMethods'] ?? $this->throwException(new InvalidArgument('Missing required field "AllowedMethods".'));
        $this->allowedOrigins = $input['AllowedOrigins'] ?? $this->throwException(new InvalidArgument('Missing required field "AllowedOrigins".'));
        $this->exposeHeaders = $input['ExposeHeaders'] ?? null;
        $this->maxAgeSeconds = $input['MaxAgeSeconds'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getAllowedHeaders(): array
    {
        return $this->allowedHeaders ?? [];
    }
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
    public function getAllowedOrigins(): array
    {
        return $this->allowedOrigins;
    }
    public function getExposeHeaders(): array
    {
        return $this->exposeHeaders ?? [];
    }
    public function getId(): ?string
    {
        return $this->id;
    }
    public function getMaxAgeSeconds(): ?int
    {
        return $this->maxAgeSeconds;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->id) {
            $node->appendChild($document->createElement('ID', $v));
        }
        if (null !== $v = $this->allowedHeaders) {
            foreach ($v as $item) {
                $node->appendChild($document->createElement('AllowedHeader', $item));
            }
        }
        $v = $this->allowedMethods;
        foreach ($v as $item) {
            $node->appendChild($document->createElement('AllowedMethod', $item));
        }
        $v = $this->allowedOrigins;
        foreach ($v as $item) {
            $node->appendChild($document->createElement('AllowedOrigin', $item));
        }
        if (null !== $v = $this->exposeHeaders) {
            foreach ($v as $item) {
                $node->appendChild($document->createElement('ExposeHeader', $item));
            }
        }
        if (null !== $v = $this->maxAgeSeconds) {
            $node->appendChild($document->createElement('MaxAgeSeconds', (string) $v));
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
