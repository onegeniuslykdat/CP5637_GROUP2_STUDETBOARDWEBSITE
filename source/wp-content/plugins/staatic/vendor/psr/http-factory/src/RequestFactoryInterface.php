<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface RequestFactoryInterface
{
    /**
     * @param string $method
     */
    public function createRequest($method, $uri): RequestInterface;
}
