<?php

namespace Staatic\Vendor\Psr\Http\Client;

use Staatic\Vendor\Psr\Http\Message\RequestInterface;
interface RequestExceptionInterface extends ClientExceptionInterface
{
    public function getRequest(): RequestInterface;
}
