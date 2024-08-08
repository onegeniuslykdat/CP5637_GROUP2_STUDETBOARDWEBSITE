<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface StreamFactoryInterface
{
    /**
     * @param string $content
     */
    public function createStream($content = ''): StreamInterface;
    /**
     * @param string $filename
     * @param string $mode
     */
    public function createStreamFromFile($filename, $mode = 'r'): StreamInterface;
    public function createStreamFromResource($resource): StreamInterface;
}
