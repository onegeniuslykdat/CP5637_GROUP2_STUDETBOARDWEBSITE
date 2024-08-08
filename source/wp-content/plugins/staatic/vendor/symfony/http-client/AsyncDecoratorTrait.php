<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\ResponseStream;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
trait AsyncDecoratorTrait
{
    use DecoratorTrait;
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    abstract public function request($method, $url, $options = []): ResponseInterface;
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof AsyncResponse) {
            $responses = [$responses];
        }
        return new ResponseStream(AsyncResponse::stream($responses, $timeout, static::class));
    }
}
