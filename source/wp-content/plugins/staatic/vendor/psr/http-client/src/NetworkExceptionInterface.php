<?php

namespace Staatic\Vendor\Psr\Http\Client;

use Staatic\Vendor\Psr\Http\Message\RequestInterface;
interface NetworkExceptionInterface extends ClientExceptionInterface
{
    public function getRequest(): RequestInterface;
}
