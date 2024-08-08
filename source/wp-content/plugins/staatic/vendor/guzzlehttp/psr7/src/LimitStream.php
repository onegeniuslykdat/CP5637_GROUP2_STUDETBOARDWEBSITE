<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use RuntimeException;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class LimitStream implements StreamInterface
{
    use StreamDecoratorTrait;
    private $offset;
    private $limit;
    private $stream;
    public function __construct(StreamInterface $stream, int $limit = -1, int $offset = 0)
    {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }
    public function eof(): bool
    {
        if ($this->stream->eof()) {
            return \true;
        }
        if ($this->limit === -1) {
            return \false;
        }
        return $this->stream->tell() >= $this->offset + $this->limit;
    }
    public function getSize(): ?int
    {
        if (null === $length = $this->stream->getSize()) {
            return null;
        } elseif ($this->limit === -1) {
            return $length - $this->offset;
        } else {
            return min($this->limit, $length - $this->offset);
        }
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        if ($whence !== \SEEK_SET || $offset < 0) {
            throw new RuntimeException(sprintf('Cannot seek to offset %s with whence %s', $offset, $whence));
        }
        $offset += $this->offset;
        if ($this->limit !== -1) {
            if ($offset > $this->offset + $this->limit) {
                $offset = $this->offset + $this->limit;
            }
        }
        $this->stream->seek($offset);
    }
    public function tell(): int
    {
        return $this->stream->tell() - $this->offset;
    }
    /**
     * @param int $offset
     */
    public function setOffset($offset): void
    {
        $current = $this->stream->tell();
        if ($current !== $offset) {
            if ($this->stream->isSeekable()) {
                $this->stream->seek($offset);
            } elseif ($current > $offset) {
                throw new RuntimeException("Could not seek to stream offset {$offset}");
            } else {
                $this->stream->read($offset - $current);
            }
        }
        $this->offset = $offset;
    }
    /**
     * @param int $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }
    public function read($length): string
    {
        if ($this->limit === -1) {
            return $this->stream->read($length);
        }
        $remaining = $this->offset + $this->limit - $this->stream->tell();
        if ($remaining > 0) {
            return $this->stream->read(min($remaining, $length));
        }
        return '';
    }
}
