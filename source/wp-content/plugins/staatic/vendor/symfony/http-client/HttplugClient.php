<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use SplObjectStorage;
use InvalidArgumentException;
use BadMethodCallException;
use Staatic\Vendor\GuzzleHttp\Promise\Promise as GuzzlePromise;
use Staatic\Vendor\GuzzleHttp\Promise\RejectedPromise;
use Staatic\Vendor\GuzzleHttp\Promise\Utils;
use Staatic\Vendor\Http\Client\Exception\NetworkException;
use Staatic\Vendor\Http\Client\Exception\RequestException;
use Staatic\Vendor\Http\Client\HttpAsyncClient;
use Staatic\Vendor\Http\Client\HttpClient as HttplugInterface;
use Staatic\Vendor\Http\Discovery\Exception\NotFoundException;
use Staatic\Vendor\Http\Discovery\Psr17FactoryDiscovery;
use Staatic\Vendor\Http\Message\RequestFactory;
use Staatic\Vendor\Http\Message\StreamFactory;
use Staatic\Vendor\Http\Message\UriFactory;
use Staatic\Vendor\Nyholm\Psr7\Factory\Psr17Factory;
use Staatic\Vendor\Nyholm\Psr7\Request;
use Staatic\Vendor\Nyholm\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\RequestFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\StreamFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UriFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\HttplugWaitLoop;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\HttplugPromise;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
if (!interface_exists(HttplugInterface::class)) {
    throw new LogicException('You cannot use "Symfony\Component\HttpClient\HttplugClient" as the "php-http/httplug" package is not installed. Try running "composer require php-http/httplug".');
}
if (!interface_exists(RequestFactory::class)) {
    throw new LogicException('You cannot use "Symfony\Component\HttpClient\HttplugClient" as the "php-http/message-factory" package is not installed. Try running "composer require php-http/message-factory".');
}
if (!interface_exists(RequestFactoryInterface::class)) {
    throw new LogicException('You cannot use the "Symfony\Component\HttpClient\HttplugClient" as the "psr/http-factory" package is not installed. Try running "composer require nyholm/psr7".');
}
final class HttplugClient implements HttplugInterface, HttpAsyncClient, RequestFactoryInterface, StreamFactoryInterface, UriFactoryInterface, RequestFactory, StreamFactory, UriFactory, ResetInterface
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
    /**
     * @var SplObjectStorage|null
     */
    private $promisePool;
    /**
     * @var HttplugWaitLoop
     */
    private $waitLoop;
    public function __construct(HttpClientInterface $client = null, ResponseFactoryInterface $responseFactory = null, StreamFactoryInterface $streamFactory = null)
    {
        $this->client = $client ?? HttpClient::create();
        $streamFactory = $streamFactory ?? (($responseFactory instanceof StreamFactoryInterface) ? $responseFactory : null);
        $this->promisePool = class_exists(Utils::class) ? new SplObjectStorage() : null;
        if (null === $responseFactory || null === $streamFactory) {
            if (!class_exists(Psr17Factory::class) && !class_exists(Psr17FactoryDiscovery::class)) {
                throw new LogicException('You cannot use the "Symfony\Component\HttpClient\HttplugClient" as no PSR-17 factories have been provided. Try running "composer require nyholm/psr7".');
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
        $this->waitLoop = new HttplugWaitLoop($this->client, $this->promisePool, $this->responseFactory, $this->streamFactory);
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
    public function sendRequest($request)
    {
        try {
            return $this->waitLoop->createPsr7Response($this->sendPsr7Request($request));
        } catch (TransportExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), $request, $e);
        }
    }
    /**
     * @param RequestInterface $request
     */
    public function sendAsyncRequest($request): HttplugPromise
    {
        if (!$promisePool = $this->promisePool) {
            throw new LogicException(sprintf('You cannot use "%s()" as the "guzzlehttp/promises" package is not installed. Try running "composer require guzzlehttp/promises".', __METHOD__));
        }
        try {
            $response = $this->sendPsr7Request($request, \true);
        } catch (NetworkException $e) {
            return new HttplugPromise(new RejectedPromise($e));
        }
        $waitLoop = $this->waitLoop;
        $promise = new GuzzlePromise(static function () use ($response, $waitLoop) {
            $waitLoop->wait($response);
        }, static function () use ($response, $promisePool) {
            $response->cancel();
            unset($promisePool[$response]);
        });
        $promisePool[$response] = [$request, $promise];
        return new HttplugPromise($promise);
    }
    /**
     * @param float|null $maxDuration
     * @param float|null $idleTimeout
     */
    public function wait($maxDuration = null, $idleTimeout = null): int
    {
        return $this->waitLoop->wait(null, $maxDuration, $idleTimeout);
    }
    /**
     * @param mixed[] $headers
     */
    public function createRequest($method, $uri, $headers = [], $body = null, $protocolVersion = '1.1'): RequestInterface
    {
        if (2 < \func_num_args()) {
            trigger_deprecation('symfony/http-client', '6.2', 'Passing more than 2 arguments to "%s()" is deprecated.', __METHOD__);
        }
        if ($this->responseFactory instanceof RequestFactoryInterface) {
            $request = $this->responseFactory->createRequest($method, $uri);
        } elseif (class_exists(Request::class)) {
            $request = new Request($method, $uri);
        } elseif (class_exists(Psr17FactoryDiscovery::class)) {
            $request = Psr17FactoryDiscovery::findRequestFactory()->createRequest($method, $uri);
        } else {
            throw new LogicException(sprintf('You cannot use "%s()" as the "nyholm/psr7" package is not installed. Try running "composer require nyholm/psr7".', __METHOD__));
        }
        $request = $request->withProtocolVersion($protocolVersion)->withBody($this->createStream($body ?? ''));
        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }
        return $request;
    }
    public function createStream($content = ''): StreamInterface
    {
        if (!\is_string($content)) {
            trigger_deprecation('symfony/http-client', '6.2', 'Passing a "%s" to "%s()" is deprecated, use "createStreamFrom*()" instead.', get_debug_type($content), __METHOD__);
        }
        if ($content instanceof StreamInterface) {
            return $content;
        }
        if (\is_string($content ?? '')) {
            $stream = $this->streamFactory->createStream($content ?? '');
        } elseif (\is_resource($content)) {
            $stream = $this->streamFactory->createStreamFromResource($content);
        } else {
            throw new InvalidArgumentException(sprintf('"%s()" expects string, resource or StreamInterface, "%s" given.', __METHOD__, get_debug_type($content)));
        }
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
    public function createUri($uri = ''): UriInterface
    {
        if (!\is_string($uri)) {
            trigger_deprecation('symfony/http-client', '6.2', 'Passing a "%s" to "%s()" is deprecated, pass a string instead.', get_debug_type($uri), __METHOD__);
        }
        if ($uri instanceof UriInterface) {
            return $uri;
        }
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
    public function __sleep(): array
    {
        throw new BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    public function __wakeup()
    {
        throw new BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    public function __destruct()
    {
        $this->wait();
    }
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
    private function sendPsr7Request(RequestInterface $request, bool $buffer = null): ResponseInterface
    {
        try {
            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->seek(0);
            }
            $options = ['headers' => $request->getHeaders(), 'body' => $body->getContents(), 'buffer' => $buffer];
            if ('1.0' === $request->getProtocolVersion()) {
                $options['http_version'] = '1.0';
            }
            return $this->client->request($request->getMethod(), (string) $request->getUri(), $options);
        } catch (InvalidArgumentException $e) {
            throw new RequestException($e->getMessage(), $request, $e);
        } catch (TransportExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), $request, $e);
        }
    }
}
