<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use BadMethodCallException;
use LogicException;
use Throwable;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class FnStream implements StreamInterface
{
    private const SLOTS = ['__toString', 'close', 'detach', 'rewind', 'getSize', 'tell', 'eof', 'isSeekable', 'seek', 'isWritable', 'write', 'isReadable', 'read', 'getContents', 'getMetadata'];
    private $methods;
    public function __construct(array $methods)
    {
        $this->methods = $methods;
        foreach ($methods as $name => $fn) {
            $this->{'_fn_' . $name} = $fn;
        }
    }
    public function __get(string $name): void
    {
        throw new BadMethodCallException(str_replace('_fn_', '', $name) . '() is not implemented in the FnStream');
    }
    public function __destruct()
    {
        if (isset($this->_fn_close)) {
            ($this->_fn_close)();
        }
    }
    public function __wakeup(): void
    {
        throw new LogicException('FnStream should never be unserialized');
    }
    /**
     * @param StreamInterface $stream
     * @param mixed[] $methods
     */
    public static function decorate($stream, $methods)
    {
        foreach (array_diff(self::SLOTS, array_keys($methods)) as $diff) {
            $callable = [$stream, $diff];
            $methods[$diff] = $callable;
        }
        return new self($methods);
    }
    public function __toString(): string
    {
        try {
            return ($this->_fn___toString)();
        } catch (Throwable $e) {
            if (\PHP_VERSION_ID >= 70400) {
                throw $e;
            }
            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), \E_USER_ERROR);
            return '';
        }
    }
    public function close(): void
    {
        ($this->_fn_close)();
    }
    public function detach()
    {
        return ($this->_fn_detach)();
    }
    public function getSize(): ?int
    {
        return ($this->_fn_getSize)();
    }
    public function tell(): int
    {
        return ($this->_fn_tell)();
    }
    public function eof(): bool
    {
        return ($this->_fn_eof)();
    }
    public function isSeekable(): bool
    {
        return ($this->_fn_isSeekable)();
    }
    public function rewind(): void
    {
        ($this->_fn_rewind)();
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        ($this->_fn_seek)($offset, $whence);
    }
    public function isWritable(): bool
    {
        return ($this->_fn_isWritable)();
    }
    public function write($string): int
    {
        return ($this->_fn_write)($string);
    }
    public function isReadable(): bool
    {
        return ($this->_fn_isReadable)();
    }
    public function read($length): string
    {
        return ($this->_fn_read)($length);
    }
    public function getContents(): string
    {
        return ($this->_fn_getContents)();
    }
    public function getMetadata($key = null)
    {
        return ($this->_fn_getMetadata)($key);
    }
}
