<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use InvalidArgumentException;
use RuntimeException;
use Staatic\Vendor\Http\Discovery\Exception\NotFoundException;
use Staatic\Vendor\Http\Discovery\Psr17FactoryDiscovery;
use Staatic\Vendor\Nyholm\Psr7\Factory\Psr17Factory;
use Staatic\Vendor\Nyholm\Psr7\Request;
use Staatic\Vendor\Nyholm\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Client\ClientInterface;
use Staatic\Vendor\Psr\Http\Client\NetworkExceptionInterface;
use Staatic\Vendor\Psr\Http\Client\RequestExceptionInterface;
use Staatic\Vendor\Psr\Http\Message\RequestFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\StreamFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UriFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\StreamableInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\StreamWrapper;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
if (!interface_exists(RequestFactoryInterface::class)) {
    throw new LogicException('You cannot use the "Symfony\Component\HttpClient\Psr18Client" as the "psr/http-factory" package is not installed. Try running "composer require nyholm/psr7".');
}
if (!interface_exists(ClientInterface::class)) {
    throw new LogicException('You cannot use the "Symfony\Component\HttpClient\Psr18Client" as the "psr/http-client" package is not installed. Try running "composer require psr/http-client".');
}
final class Psr18Client implements ClientInterface, RequestFactoryInterface, StreamFactoryInterface, UriFactoryInterface, ResetInterface
{
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;
    public function __construct(HttpClientInterface $client = null, ResponseFactoryInterface $responseFactory = null, StreamFactoryInterface $streamFactory = null)
    {
        $this->client = $client ?? HttpClient::create();
        $streamFactory = $streamFactory ?? (($responseFactory instanceof StreamFactoryInterface) ? $responseFactory : null);
        if (null === $responseFactory || null === $streamFactory) {
            if (!class_exists(Psr17Factory::class) && !class_exists(Psr17FactoryDiscovery::class)) {
                throw new LogicException('You cannot use the "Symfony\Component\HttpClient\Psr18Client" as no PSR-17 factories have been provided. Try running "composer require nyholm/psr7".');
            }
            try {
                $psr17Factory = class_exists(Psr17Factory::class, \false) ? new Psr17Factory() : null;
                $responseFactory = $responseFactory ?? $psr17Factory ?? Psr17FactoryDiscovery::findResponseFactory();
                $streamFactory = $streamFactory ?? $psr17Factory ?? Psr17FactoryDiscovery::findStreamFactory();
            } catch (NotFoundException $e) {
                throw new LogicException('You cannot use the "Symfony\Component\HttpClient\HttplugClient" as no PSR-17 factories have been found. Try running "composer require nyholm/psr7".', 0, $e);
            }
        }
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }
    /**
     * @param mixed[] $options
     * @return static
     */
    public function withOptions($options)
    {
        $clone = clone $this;
        $clone->client = $clone->client->withOptions($options);
        return $clone;
    }
    /**
     * @param RequestInterface $request
     */
    public function sendRequest($request): ResponseInterface
    {
        try {
            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->seek(0);
            }
            $options = ['headers' => $request->getHeaders(), 'body' => $body->getContents()];
            if ('1.0' === $request->getProtocolVersion()) {
                $options['http_version'] = '1.0';
            }
            $response = $this->client->request($request->getMethod(), (string) $request->getUri(), $options);
            $psrResponse = $this->responseFactory->createResponse($response->getStatusCode());
            foreach ($response->getHeaders(\false) as $name => $values) {
                foreach ($values as $value) {
                    try {
                        $psrResponse = $psrResponse->withAddedHeader($name, $value);
                    } catch (InvalidArgumentException $e) {
                    }
                }
            }
            $body = ($response instanceof StreamableInterface) ? $response->toStream(\false) : StreamWrapper::createResource($response, $this->client);
            $body = $this->streamFactory->createStreamFromResource($body);
            if ($body->isSeekable()) {
                $body->seek(0);
            }
            return $psrResponse->withBody($body);
        } catch (TransportExceptionInterface $e) {
            if ($e instanceof InvalidArgumentException) {
                throw new Psr18RequestException($e, $request);
            }
            throw new Psr18NetworkException($e, $request);
        }
    }
    /**
     * @param string $method
     */
    public function createRequest($method, $uri): RequestInterface
    {
        if ($this->responseFactory instanceof RequestFactoryInterface) {
            return $this->responseFactory->createRequest($method, $uri);
        }
        if (class_exists(Request::class)) {
            return new Request($method, $uri);
        }
        if (class_exists(Psr17FactoryDiscovery::class)) {
            return Psr17FactoryDiscovery::findRequestFactory()->createRequest($method, $uri);
        }
        throw new LogicException(sprintf('You cannot use "%s()" as the "nyholm/psr7" package is not installed. Try running "composer require nyholm/psr7".', __METHOD__));
    }
    /**
     * @param string $content
     */
    public function createStream($content = ''): StreamInterface
    {
        $stream = $this->streamFactory->createStream($content);
        if ($stream->isSeekable()) {
            $stream->seek(0);
        }
        return $stream;
    }
    /**
     * @param string $filename
     * @param string $mode
     */
    public function createStreamFromFile($filename, $mode = 'r'): StreamInterface
    {
        return $this->streamFactory->createStreamFromFile($filename, $mode);
    }
    public function createStreamFromResource($resource): StreamInterface
    {
        return $this->streamFactory->createStreamFromResource($resource);
    }
    /**
     * @param string $uri
     */
    public function createUri($uri = ''): UriInterface
    {
        if ($this->responseFactory instanceof UriFactoryInterface) {
            return $this->responseFactory->createUri($uri);
        }
        if (class_exists(Uri::class)) {
            return new Uri($uri);
        }
        if (class_exists(Psr17FactoryDiscovery::class)) {
            return Psr17FactoryDiscovery::findUrlFactory()->createUri($uri);
        }
        throw new LogicException(sprintf('You cannot use "%s()" as the "nyholm/psr7" package is not installed. Try running "composer require nyholm/psr7".', __METHOD__));
    }
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
class Psr18NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    public function __construct(TransportExceptionInterface $e, RequestInterface $request)
    {
        parent::__construct($e->getMessage(), 0, $e);
        $this->request = $request;
    }
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
class Psr18RequestException extends InvalidArgumentException implements RequestExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    public function __construct(TransportExceptionInterface $e, RequestInterface $request)
    {
        parent::__construct($e->getMessage(), 0, $e);
        $this->request = $request;
    }
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
