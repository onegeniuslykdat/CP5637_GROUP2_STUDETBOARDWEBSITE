<?php

namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Traversable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class StringStream implements RequestStream
{
    private $content;
    private $lengthCache;
    private function __construct(string $content)
    {
        $this->content = $content;
    }
    public static function create($content): StringStream
    {
        if ($content instanceof self) {
            return $content;
        }
        if ($content instanceof RequestStream) {
            return new self($content->stringify());
        }
        if (\is_string($content)) {
            return new self($content);
        }
        throw new InvalidArgument(sprintf('Expect content to be a "%s" or as "string". "%s" given.', RequestStream::class, \is_object($content) ? \get_class($content) : \gettype($content)));
    }
    public function length(): int
    {
        return $this->lengthCache ?? $this->lengthCache = \strlen($this->content);
    }
    public function stringify(): string
    {
        return $this->content;
    }
    public function getIterator(): Traversable
    {
        yield $this->content;
    }
    /**
     * @param string $algo
     * @param bool $raw
     */
    public function hash($algo = 'sha256', $raw = \false): string
    {
        return hash($algo, $this->content, $raw);
    }
}
