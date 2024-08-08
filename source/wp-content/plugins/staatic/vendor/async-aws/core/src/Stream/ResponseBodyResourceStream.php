<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Staatic\Vendor\AsyncAws\Core\Exception\RuntimeException;
class ResponseBodyResourceStream implements ResultStream
{
    private $resource;
    public function __construct($resource)
    {
        $this->resource = $resource;
    }
    public function __toString(): string
    {
        return $this->getContentAsString();
    }
    public function getChunks(): iterable
    {
        $pos = ftell($this->resource);
        if (0 !== $pos && !rewind($this->resource)) {
            throw new RuntimeException('The stream is not rewindable');
        }
        try {
            while (!feof($this->resource)) {
                yield fread($this->resource, 64 * 1024);
            }
        } finally {
            fseek($this->resource, $pos);
        }
    }
    public function getContentAsString(): string
    {
        $pos = ftell($this->resource);
        try {
            if (!rewind($this->resource)) {
                throw new RuntimeException('Failed to rewind the stream');
            }
            return stream_get_contents($this->resource);
        } finally {
            fseek($this->resource, $pos);
        }
    }
    public function getContentAsResource()
    {
        if (!rewind($this->resource)) {
            throw new RuntimeException('Failed to rewind the stream');
        }
        return $this->resource;
    }
}
