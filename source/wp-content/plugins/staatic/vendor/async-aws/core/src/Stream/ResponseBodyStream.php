<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Staatic\Vendor\AsyncAws\Core\Exception\LogicException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
class ResponseBodyStream implements ResultStream
{
    private $responseStream;
    private $fallback;
    private $partialRead = \false;
    public function __construct(ResponseStreamInterface $responseStream)
    {
        $this->responseStream = $responseStream;
    }
    public function __toString(): string
    {
        return $this->getContentAsString();
    }
    public function getChunks(): iterable
    {
        if (null !== $this->fallback) {
            yield from $this->fallback->getChunks();
            return;
        }
        if ($this->partialRead) {
            throw new LogicException(sprintf('You can not call "%s". Another process doesn\'t reading "getChunks" till the end.', __METHOD__));
        }
        $resource = fopen('php://temp', 'rb+');
        foreach ($this->responseStream as $chunk) {
            $this->partialRead = \true;
            $chunkContent = $chunk->getContent();
            fwrite($resource, $chunkContent);
            yield $chunkContent;
        }
        $this->fallback = new ResponseBodyResourceStream($resource);
        $this->partialRead = \false;
    }
    public function getContentAsString(): string
    {
        if (null === $this->fallback) {
            foreach ($this->getChunks() as $chunk) {
            }
            \assert(null !== $this->fallback);
        }
        return $this->fallback->getContentAsString();
    }
    public function getContentAsResource()
    {
        if (null === $this->fallback) {
            foreach ($this->getChunks() as $chunk) {
            }
            \assert(null !== $this->fallback);
        }
        return $this->fallback->getContentAsResource();
    }
}
