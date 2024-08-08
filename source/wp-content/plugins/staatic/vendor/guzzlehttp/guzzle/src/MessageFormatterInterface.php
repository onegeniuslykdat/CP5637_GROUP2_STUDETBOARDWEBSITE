<?php

namespace Staatic\Vendor\GuzzleHttp;

use Throwable;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
interface MessageFormatterInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @param Throwable|null $error
     */
    public function format($request, $response = null, $error = null): string;
}
