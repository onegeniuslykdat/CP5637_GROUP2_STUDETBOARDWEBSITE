<?php

namespace Staatic\Vendor\Psr\Http\Client;

use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * @param RequestInterface $request
     */
    public function sendRequest($request): ResponseInterface;
}
