<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

use Staatic\Vendor\Symfony\Component\HttpClient\Response\CurlResponse;
final class PushedResponse
{
    /**
     * @var CurlResponse
     */
    public $response;
    /**
     * @var mixed[]
     */
    public $requestHeaders;
    /**
     * @var mixed[]
     */
    public $parentOptions = [];
    public $handle;
    public function __construct(CurlResponse $response, array $requestHeaders, array $parentOptions, $handle)
    {
        $this->response = $response;
        $this->requestHeaders = $requestHeaders;
        $this->parentOptions = $parentOptions;
        $this->handle = $handle;
    }
}
