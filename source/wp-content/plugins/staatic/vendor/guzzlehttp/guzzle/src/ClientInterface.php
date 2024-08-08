<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\GuzzleHttp\Exception\GuzzleException;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface ClientInterface
{
    public const MAJOR_VERSION = 7;
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     */
    public function send($request, $options = []): ResponseInterface;
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     */
    public function sendAsync($request, $options = []): PromiseInterface;
    /**
     * @param string $method
     * @param mixed[] $options
     */
    public function request($method, $uri, $options = []): ResponseInterface;
    /**
     * @param string $method
     * @param mixed[] $options
     */
    public function requestAsync($method, $uri, $options = []): PromiseInterface;
    /**
     * @param string|null $option
     */
    public function getConfig($option = null);
}
