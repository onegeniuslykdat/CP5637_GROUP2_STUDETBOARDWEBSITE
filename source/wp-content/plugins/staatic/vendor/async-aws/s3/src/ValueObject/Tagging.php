<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Tagging
{
    private $tagSet;
    public function __construct(array $input)
    {
        $this->tagSet = isset($input['TagSet']) ? array_map([Tag::class, 'create'], $input['TagSet']) : $this->throwException(new InvalidArgument('Missing required field "TagSet".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getTagSet(): array
    {
        return $this->tagSet;
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        $v = $this->tagSet;
        $node->appendChild($nodeList = $document->createElement('TagSet'));
        foreach ($v as $item) {
            $nodeList->appendChild($child = $document->createElement('Tag'));
            $item->requestBody($child, $document);
        }
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
