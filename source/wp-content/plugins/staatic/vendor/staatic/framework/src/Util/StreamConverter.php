<?php

namespace Staatic\Framework\Util;

use Staatic\Vendor\GuzzleHttp\Psr7\StreamWrapper;
use LogicException;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class StreamConverter
{
    public static function streamToResource(StreamInterface $stream)
    {
        if (!$stream->getMetadata('uri')) {
            return StreamWrapper::getResource($stream);
        }
        if (($resource = $stream->detach()) === null) {
            throw new LogicException("Unable to convert stream into resource.");
        }
        return $resource;
    }
}
