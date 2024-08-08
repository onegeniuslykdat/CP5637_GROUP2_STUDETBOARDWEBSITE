<?php

namespace Staatic\Vendor\GuzzleHttp\Handler;

use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
class CurlHandler
{
    private $factory;
    public function __construct(array $options = [])
    {
        $this->factory = $options['handle_factory'] ?? new CurlFactory(3);
    }
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        if (isset($options['delay'])) {
            \usleep($options['delay'] * 1000);
        }
        $easy = $this->factory->create($request, $options);
        \curl_exec($easy->handle);
        $easy->errno = \curl_errno($easy->handle);
        return CurlFactory::finish($this, $easy, $this->factory);
    }
}
