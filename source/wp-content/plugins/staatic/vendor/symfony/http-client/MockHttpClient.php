<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Iterator;
use Closure;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\MockResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\ResponseStream;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
class MockHttpClient implements HttpClientInterface, ResetInterface
{
    use HttpClientTrait;
    /**
     * @var ResponseInterface|Closure|iterable|null
     */
    private $responseFactory;
    /**
     * @var int
     */
    private $requestsCount = 0;
    /**
     * @var mixed[]
     */
    private $defaultOptions = [];
    /**
     * @param callable|iterable|ResponseInterface $responseFactory
     */
    public function __construct($responseFactory = null, ?string $baseUri = 'https://example.com')
    {
        $this->setResponseFactory($responseFactory);
        $this->defaultOptions['base_uri'] = $baseUri;
    }
    public function setResponseFactory($responseFactory): void
    {
        if ($responseFactory instanceof ResponseInterface) {
            $responseFactory = [$responseFactory];
        }
        if (!$responseFactory instanceof Iterator && null !== $responseFactory && !\is_callable($responseFactory)) {
            $responseFactory = (static function () use ($responseFactory) {
                yield from $responseFactory;
            })();
        }
        $this->responseFactory = (!\is_callable($responseFactory)) ? $responseFactory : Closure::fromCallable($responseFactory);
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        [$url, $options] = $this->prepareRequest($method, $url, $options, $this->defaultOptions, \true);
        $url = implode('', $url);
        if (null === $this->responseFactory) {
            $response = new MockResponse();
        } elseif (\is_callable($this->responseFactory)) {
            $response = ($this->responseFactory)($method, $url, $options);
        } elseif (!$this->responseFactory->valid()) {
            throw new TransportException('The response factory iterator passed to MockHttpClient is empty.');
        } else {
            $responseFactory = $this->responseFactory->current();
            $response = \is_callable($responseFactory) ? $responseFactory($method, $url, $options) : $responseFactory;
            $this->responseFactory->next();
        }
        ++$this->requestsCount;
        if (!$response instanceof ResponseInterface) {
            throw new TransportException(sprintf('The response factory passed to MockHttpClient must return/yield an instance of ResponseInterface, "%s" given.', get_debug_type($response)));
        }
        return MockResponse::fromRequest($method, $url, $options, $response);
    }
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof ResponseInterface) {
            $responses = [$responses];
        }
        return new ResponseStream(MockResponse::stream($responses, $timeout));
    }
    public function getRequestsCount(): int
    {
        return $this->requestsCount;
    }
    /**
     * @param mixed[] $options
     * @return static
     */
    public function withOptions($options)
    {
        $clone = clone $this;
        $clone->defaultOptions = self::mergeDefaultOptions($options, $this->defaultOptions, \true);
        return $clone;
    }
    public function reset()
    {
        $this->requestsCount = 0;
    }
}
