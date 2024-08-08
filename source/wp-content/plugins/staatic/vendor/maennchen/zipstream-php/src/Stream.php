<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream;

use function mb_strlen;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use RuntimeException;
class Stream implements StreamInterface
{
    protected $stream;
    public function __construct($stream)
    {
        $this->stream = $stream;
    }
    public function __toString(): string
    {
        try {
            $this->seek(0);
        } catch (RuntimeException $e) {
        }
        return (string) stream_get_contents($this->stream);
    }
    public function close(): void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->detach();
    }
    public function detach()
    {
        $result = $this->stream;
        $this->stream = null;
        return $result;
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException();
        }
        if (fseek($this->stream, $offset, $whence) !== 0) {
            throw new RuntimeException();
        }
    }
    public function isSeekable(): bool
    {
        return (bool) $this->getMetadata('seekable');
    }
    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->stream);
        return ($key !== null) ? @$metadata[$key] : $metadata;
    }
    public function getSize(): ?int
    {
        $stats = fstat($this->stream);
        return $stats['size'];
    }
    public function tell(): int
    {
        $position = ftell($this->stream);
        if ($position === \false) {
            throw new RuntimeException();
        }
        return $position;
    }
    public function eof(): bool
    {
        return feof($this->stream);
    }
    public function rewind(): void
    {
        $this->seek(0);
    }
    public function write($string): int
    {
        if (!$this->isWritable()) {
            throw new RuntimeException();
        }
        if (!fwrite($this->stream, $string)) {
            throw new RuntimeException();
        }
        return mb_strlen($string);
    }
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
        if (!is_string($mode)) {
            throw new RuntimeException('Could not get stream mode from metadata!');
        }
        return preg_match('/[waxc+]/', $mode) === 1;
    }
    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException();
        }
        $result = fread($this->stream, $length);
        if ($result === \false) {
            throw new RuntimeException();
        }
        return $result;
    }
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
        if (!is_string($mode)) {
            throw new RuntimeException('Could not get stream mode from metadata!');
        }
        return preg_match('/[r+]/', $mode) === 1;
    }
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException();
        }
        $result = stream_get_contents($this->stream);
        if ($result === \false) {
            throw new RuntimeException();
        }
        return $result;
    }
}
