<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface ServerRequestFactoryInterface
{
    /**
     * @param string $method
     * @param mixed[] $serverParams
     */
    public function createServerRequest($method, $uri, $serverParams = []): ServerRequestInterface;
}
