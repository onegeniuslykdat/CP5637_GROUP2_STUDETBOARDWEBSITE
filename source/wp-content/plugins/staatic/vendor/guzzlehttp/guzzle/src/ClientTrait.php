<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\GuzzleHttp\Exception\GuzzleException;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
trait ClientTrait
{
    /**
     * @param string $method
     * @param mixed[] $options
     */
    abstract public function request($method, $uri, $options = []): ResponseInterface;
    /**
     * @param mixed[] $options
     */
    public function get($uri, $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function head($uri, $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function put($uri, $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function post($uri, $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function patch($uri, $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function delete($uri, $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }
    /**
     * @param string $method
     * @param mixed[] $options
     */
    abstract public function requestAsync($method, $uri, $options = []): PromiseInterface;
    /**
     * @param mixed[] $options
     */
    public function getAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('GET', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function headAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('HEAD', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function putAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('PUT', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function postAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('POST', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function patchAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('PATCH', $uri, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function deleteAsync($uri, $options = []): PromiseInterface
    {
        return $this->requestAsync('DELETE', $uri, $options);
    }
}
