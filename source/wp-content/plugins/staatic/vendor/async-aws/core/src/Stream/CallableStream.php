<?php

namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Traversable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class CallableStream implements ReadOnceResultStream, RequestStream
{
    private $content;
    private $chunkSize;
    private function __construct(callable $content, int $chunkSize = 64 * 1024)
    {
        $this->content = $content;
        $this->chunkSize = $chunkSize;
    }
    /**
     * @param int $chunkSize
     */
    public static function create($content, $chunkSize = 64 * 1024): CallableStream
    {
        if ($content instanceof self) {
            return $content;
        }
        if (\is_callable($content)) {
            return new self($content, $chunkSize);
        }
        throw new InvalidArgument(sprintf('Expect content to be a "callable". "%s" given.', \is_object($content) ? \get_class($content) : \gettype($content)));
    }
    public function length(): ?int
    {
        return null;
    }
    public function stringify(): string
    {
        return implode('', iterator_to_array($this));
    }
    public function getIterator(): Traversable
    {
        while (\true) {
            if (!\is_string($data = ($this->content)($this->chunkSize))) {
                throw new InvalidArgument(sprintf('The return value of content callback must be a string, %s returned.', \is_object($data) ? \get_class($data) : \gettype($data)));
            }
            if ('' === $data) {
                break;
            }
            yield $data;
        }
    }
    /**
     * @param string $algo
     * @param bool $raw
     */
    public function hash($algo = 'sha256', $raw = \false): string
    {
        $ctx = hash_init($algo);
        foreach ($this as $chunk) {
            hash_update($ctx, $chunk);
        }
        return hash_final($ctx, $raw);
    }
}
