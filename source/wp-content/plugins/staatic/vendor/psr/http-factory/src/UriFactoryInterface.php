<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface UriFactoryInterface
{
    /**
     * @param string $uri
     */
    public function createUri($uri = ''): UriInterface;
}
