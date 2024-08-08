<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use InvalidArgumentException;
use Throwable;
use RuntimeException;
use Exception;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
class Stream implements StreamInterface
{
    private const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    private const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';
    private $stream;
    private $size;
    private $seekable;
    private $readable;
    private $writable;
    private $uri;
    private $customMetadata;
    public function __construct($stream, array $options = [])
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }
        if (isset($options['size'])) {
            $this->size = $options['size'];
        }
        $this->customMetadata = $options['metadata'] ?? [];
        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->readable = (bool) preg_match(self::READABLE_MODES, $meta['mode']);
        $this->writable = (bool) preg_match(self::WRITABLE_MODES, $meta['mode']);
        $this->uri = $this->getMetadata('uri');
    }
    public function __destruct()
    {
        $this->close();
    }
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        } catch (Throwable $e) {
            if (\PHP_VERSION_ID >= 70400) {
                throw $e;
            }
            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), \E_USER_ERROR);
            return '';
        }
    }
    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->readable) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }
        return Utils::tryGetContents($this->stream);
    }
    public function close(): void
    {
        if (isset($this->stream)) {
            if (is_resource($this->stream)) {
                fclose($this->stream);
            }
            $this->detach();
        }
    }
    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }
        $result = $this->stream;
        unset($this->stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = \false;
        return $result;
    }
    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }
        if (!isset($this->stream)) {
            return null;
        }
        if ($this->uri) {
            clearstatcache(\true, $this->uri);
        }
        $stats = fstat($this->stream);
        if (is_array($stats) && isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }
        return null;
    }
    public function isReadable(): bool
    {
        return $this->readable;
    }
    public function isWritable(): bool
    {
        return $this->writable;
    }
    public function isSeekable(): bool
    {
        return $this->seekable;
    }
    public function eof(): bool
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        return feof($this->stream);
    }
    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        $result = ftell($this->stream);
        if ($result === \false) {
            throw new RuntimeException('Unable to determine stream position');
        }
        return $result;
    }
    public function rewind(): void
    {
        $this->seek(0);
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        $whence = (int) $whence;
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->seekable) {
            throw new RuntimeException('Stream is not seekable');
        }
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Unable to seek to stream position ' . $offset . ' with whence ' . var_export($whence, \true));
        }
    }
    public function read($length): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->readable) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }
        if ($length < 0) {
            throw new RuntimeException('Length parameter cannot be negative');
        }
        if (0 === $length) {
            return '';
        }
        try {
            $string = fread($this->stream, $length);
        } catch (Exception $e) {
            throw new RuntimeException('Unable to read from stream', 0, $e);
        }
        if (\false === $string) {
            throw new RuntimeException('Unable to read from stream');
        }
        return $string;
    }
    public function write($string): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->writable) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }
        $this->size = null;
        $result = fwrite($this->stream, $string);
        if ($result === \false) {
            throw new RuntimeException('Unable to write to stream');
        }
        return $result;
    }
    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        } elseif (!$key) {
            return $this->customMetadata + stream_get_meta_data($this->stream);
        } elseif (isset($this->customMetadata[$key])) {
            return $this->customMetadata[$key];
        }
        $meta = stream_get_meta_data($this->stream);
        return $meta[$key] ?? null;
    }
}
