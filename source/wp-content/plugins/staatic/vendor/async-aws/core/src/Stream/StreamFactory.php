<?php

namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
class StreamFactory
{
    /**
     * @param int $preferredChunkSize
     */
    public static function create($content, $preferredChunkSize = 64 * 1024): RequestStream
    {
        if (null === $content || \is_string($content)) {
            return StringStream::create($content ?? '');
        }
        if (\is_callable($content)) {
            return CallableStream::create($content, $preferredChunkSize);
        }
        if (is_iterable($content)) {
            return IterableStream::create($content);
        }
        if (\is_resource($content)) {
            return ResourceStream::create($content, $preferredChunkSize);
        }
        throw new InvalidArgument(sprintf('Unexpected content type "%s".', \is_object($content) ? \get_class($content) : \gettype($content)));
    }
}
