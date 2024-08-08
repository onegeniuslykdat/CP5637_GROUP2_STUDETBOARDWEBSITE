<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use BadMethodCallException;
use Throwable;
use Closure;
use Generator;
use Staatic\Vendor\Amp\ByteStream\StreamException;
use Staatic\Vendor\Amp\CancellationTokenSource;
use Staatic\Vendor\Amp\Coroutine;
use Staatic\Vendor\Amp\Deferred;
use Staatic\Vendor\Amp\Http\Client\HttpException;
use Staatic\Vendor\Amp\Http\Client\Request;
use Staatic\Vendor\Amp\Http\Client\Response;
use Staatic\Vendor\Amp\Loop;
use Staatic\Vendor\Amp\Promise;
use Staatic\Vendor\Amp\Success;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\FirstChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\InformationalChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\HttpClientTrait;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\AmpBody;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\AmpClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\Canary;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\ClientState;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class AmpResponse implements ResponseInterface, StreamableInterface
{
    use CommonResponseTrait;
    use TransportResponseTrait;
    /**
     * @var string
     */
    private static $nextId = 'a';
    /**
     * @var AmpClientState
     */
    private $multi;
    /**
     * @var mixed[]|null
     */
    private $options;
    /**
     * @var Closure
     */
    private $onProgress;
    /**
     * @var string|null
     */
    private static $delay;
    public function __construct(AmpClientState $multi, Request $request, array $options, ?LoggerInterface $logger)
    {
        $this->multi = $multi;
        $this->options =& $options;
        $this->logger = $logger;
        $this->timeout = $options['timeout'];
        $this->shouldBuffer = $options['buffer'];
        if ($this->inflate = \extension_loaded('zlib') && !$request->hasHeader('accept-encoding')) {
            $request->setHeader('Accept-Encoding', 'gzip');
        }
        $this->initializer = static function (self $response) {
            return null !== $response->options;
        };
        $info =& $this->info;
        $headers =& $this->headers;
        $canceller = new CancellationTokenSource();
        $handle =& $this->handle;
        $info['url'] = (string) $request->getUri();
        $info['http_method'] = $request->getMethod();
        $info['start_time'] = null;
        $info['redirect_url'] = null;
        $info['original_url'] = $info['url'];
        $info['redirect_time'] = 0.0;
        $info['redirect_count'] = 0;
        $info['size_upload'] = 0.0;
        $info['size_download'] = 0.0;
        $info['upload_content_length'] = -1.0;
        $info['download_content_length'] = -1.0;
        $info['user_data'] = $options['user_data'];
        $info['max_duration'] = $options['max_duration'];
        $info['debug'] = '';
        $onProgress = $options['on_progress'] ?? static function () {
        };
        $onProgress = $this->onProgress = static function () use (&$info, $onProgress) {
            $info['total_time'] = microtime(\true) - $info['start_time'];
            $onProgress((int) $info['size_download'], (((int) (1 + $info['download_content_length'])) ?: 1) - 1, (array) $info);
        };
        $pauseDeferred = new Deferred();
        $pause = new Success();
        $throttleWatcher = null;
        $this->id = $id = self::$nextId++;
        Loop::defer(static function () use ($request, $multi, &$id, &$info, &$headers, $canceller, &$options, $onProgress, &$handle, $logger, &$pause) {
            return new Coroutine(self::generateResponse($request, $multi, $id, $info, $headers, $canceller, $options, $onProgress, $handle, $logger, $pause));
        });
        $info['pause_handler'] = static function (float $duration) use (&$throttleWatcher, &$pauseDeferred, &$pause) {
            if (null !== $throttleWatcher) {
                Loop::cancel($throttleWatcher);
            }
            $pause = $pauseDeferred->promise();
            if ($duration <= 0) {
                $deferred = $pauseDeferred;
                $pauseDeferred = new Deferred();
                $deferred->resolve();
            } else {
                $throttleWatcher = Loop::delay(ceil(1000 * $duration), static function () use (&$pauseDeferred) {
                    $deferred = $pauseDeferred;
                    $pauseDeferred = new Deferred();
                    $deferred->resolve();
                });
            }
        };
        $multi->lastTimeout = null;
        $multi->openHandles[$id] = $id;
        ++$multi->responseCount;
        $this->canary = new Canary(static function () use ($canceller, $multi, $id) {
            $canceller->cancel();
            unset($multi->openHandles[$id], $multi->handlesActivity[$id]);
        });
    }
    /**
     * @param string|null $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        return (null !== $type) ? $this->info[$type] ?? null : $this->info;
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
        try {
            $this->doDestruct();
        } finally {
            if (0 >= --$this->multi->responseCount) {
                $this->multi->responseCount = 0;
                $this->multi->dnsCache = [];
            }
        }
    }
    private static function schedule(self $response, array &$runningResponses): void
    {
        if (isset($runningResponses[0])) {
            $runningResponses[0][1][$response->id] = $response;
        } else {
            $runningResponses[0] = [$response->multi, [$response->id => $response]];
        }
        if (!isset($response->multi->openHandles[$response->id])) {
            $response->multi->handlesActivity[$response->id][] = null;
            $response->multi->handlesActivity[$response->id][] = (null !== $response->info['error']) ? new TransportException($response->info['error']) : null;
        }
    }
    private static function perform(ClientState $multi, array &$responses = null): void
    {
        if ($responses) {
            foreach ($responses as $response) {
                try {
                    if ($response->info['start_time']) {
                        $response->info['total_time'] = microtime(\true) - $response->info['start_time'];
                        ($response->onProgress)();
                    }
                } catch (Throwable $e) {
                    $multi->handlesActivity[$response->id][] = null;
                    $multi->handlesActivity[$response->id][] = $e;
                }
            }
        }
    }
    private static function select(ClientState $multi, float $timeout): int
    {
        $timeout += microtime(\true);
        self::$delay = Loop::defer(static function () use ($timeout) {
            if (0 < $timeout -= microtime(\true)) {
                self::$delay = Loop::delay(ceil(1000 * $timeout), Closure::fromCallable([Loop::class, 'stop']));
            } else {
                Loop::stop();
            }
        });
        Loop::run();
        return (null === self::$delay) ? 1 : 0;
    }
    private static function generateResponse(Request $request, AmpClientState $multi, string $id, array &$info, array &$headers, CancellationTokenSource $canceller, array &$options, Closure $onProgress, &$handle, ?LoggerInterface $logger, Promise &$pause): Generator
    {
        $request->setInformationalResponseHandler(static function (Response $response) use ($multi, $id, &$info, &$headers) {
            self::addResponseHeaders($response, $info, $headers);
            $multi->handlesActivity[$id][] = new InformationalChunk($response->getStatus(), $response->getHeaders());
            self::stopLoop();
        });
        try {
            if (null === $response = yield from self::getPushedResponse($request, $multi, $info, $headers, $options, $logger)) {
                ($nullsafeVariable1 = $logger) ? $nullsafeVariable1->info(sprintf('Request: "%s %s"', $info['http_method'], $info['url'])) : null;
                $response = yield from self::followRedirects($request, $multi, $info, $headers, $canceller, $options, $onProgress, $handle, $logger, $pause);
            }
            $options = null;
            $multi->handlesActivity[$id][] = new FirstChunk();
            if ('HEAD' === $response->getRequest()->getMethod() || \in_array($info['http_code'], [204, 304], \true)) {
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = null;
                self::stopLoop();
                return;
            }
            if ($response->hasHeader('content-length')) {
                $info['download_content_length'] = (float) $response->getHeader('content-length');
            }
            $body = $response->getBody();
            while (\true) {
                self::stopLoop();
                yield $pause;
                if (null === $data = yield $body->read()) {
                    break;
                }
                $info['size_download'] += \strlen($data);
                $multi->handlesActivity[$id][] = $data;
            }
            $multi->handlesActivity[$id][] = null;
            $multi->handlesActivity[$id][] = null;
        } catch (Throwable $e) {
            $multi->handlesActivity[$id][] = null;
            $multi->handlesActivity[$id][] = $e;
        } finally {
            $info['download_content_length'] = $info['size_download'];
        }
        self::stopLoop();
    }
    private static function followRedirects(Request $originRequest, AmpClientState $multi, array &$info, array &$headers, CancellationTokenSource $canceller, array $options, Closure $onProgress, &$handle, ?LoggerInterface $logger, Promise &$pause): Generator
    {
        yield $pause;
        $originRequest->setBody(new AmpBody($options['body'], $info, $onProgress));
        $response = yield $multi->request($options, $originRequest, $canceller->getToken(), $info, $onProgress, $handle);
        $previousUrl = null;
        while (\true) {
            self::addResponseHeaders($response, $info, $headers);
            $status = $response->getStatus();
            if (!\in_array($status, [301, 302, 303, 307, 308], \true) || null === $location = $response->getHeader('location')) {
                return $response;
            }
            $urlResolver = new class
            {
                use HttpClientTrait {
                    parseUrl as public;
                    resolveUrl as public;
                }
            };
            try {
                $previousUrl = $previousUrl ?? $urlResolver::parseUrl($info['url']);
                $location = $urlResolver::parseUrl($location);
                $location = $urlResolver::resolveUrl($location, $previousUrl);
                $info['redirect_url'] = implode('', $location);
            } catch (InvalidArgumentException $exception) {
                return $response;
            }
            if (0 >= $options['max_redirects'] || $info['redirect_count'] >= $options['max_redirects']) {
                return $response;
            }
            ($nullsafeVariable2 = $logger) ? $nullsafeVariable2->info(sprintf('Redirecting: "%s %s"', $status, $info['url'])) : null;
            try {
                while (null !== yield $response->getBody()->read()) {
                }
            } catch (HttpException|StreamException $exception) {
            }
            ++$info['redirect_count'];
            $info['url'] = $info['redirect_url'];
            $info['redirect_url'] = null;
            $previousUrl = $location;
            $request = new Request($info['url'], $info['http_method']);
            $request->setProtocolVersions($originRequest->getProtocolVersions());
            $request->setTcpConnectTimeout($originRequest->getTcpConnectTimeout());
            $request->setTlsHandshakeTimeout($originRequest->getTlsHandshakeTimeout());
            $request->setTransferTimeout($originRequest->getTransferTimeout());
            if (\in_array($status, [301, 302, 303], \true)) {
                $originRequest->removeHeader('transfer-encoding');
                $originRequest->removeHeader('content-length');
                $originRequest->removeHeader('content-type');
                if ('POST' === $response->getRequest()->getMethod() || 303 === $status) {
                    $info['http_method'] = ('HEAD' === $response->getRequest()->getMethod()) ? 'HEAD' : 'GET';
                    $request->setMethod($info['http_method']);
                }
            } else {
                $request->setBody(AmpBody::rewind($response->getRequest()->getBody()));
            }
            foreach ($originRequest->getRawHeaders() as [$name, $value]) {
                $request->addHeader($name, $value);
            }
            if ($request->getUri()->getAuthority() !== $originRequest->getUri()->getAuthority()) {
                $request->removeHeader('authorization');
                $request->removeHeader('cookie');
                $request->removeHeader('host');
            }
            yield $pause;
            $response = yield $multi->request($options, $request, $canceller->getToken(), $info, $onProgress, $handle);
            $info['redirect_time'] = microtime(\true) - $info['start_time'];
        }
    }
    private static function addResponseHeaders(Response $response, array &$info, array &$headers): void
    {
        $info['http_code'] = $response->getStatus();
        if ($headers) {
            $info['debug'] .= "< \r\n";
            $headers = [];
        }
        $h = sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatus(), $response->getReason());
        $info['debug'] .= "< {$h}\r\n";
        $info['response_headers'][] = $h;
        foreach ($response->getRawHeaders() as [$name, $value]) {
            $headers[strtolower($name)][] = $value;
            $h = $name . ': ' . $value;
            $info['debug'] .= "< {$h}\r\n";
            $info['response_headers'][] = $h;
        }
        $info['debug'] .= "< \r\n";
    }
    private static function getPushedResponse(Request $request, AmpClientState $multi, array &$info, array &$headers, array $options, ?LoggerInterface $logger): Generator
    {
        if ('' !== $options['body']) {
            return null;
        }
        $authority = $request->getUri()->getAuthority();
        foreach ($multi->pushedResponses[$authority] ?? [] as $i => [$pushedUrl, $pushDeferred, $pushedRequest, $pushedResponse, $parentOptions]) {
            if ($info['url'] !== $pushedUrl || $info['http_method'] !== $pushedRequest->getMethod()) {
                continue;
            }
            foreach ($parentOptions as $k => $v) {
                if ($options[$k] !== $v) {
                    continue 2;
                }
            }
            foreach (['authorization', 'cookie', 'range', 'proxy-authorization'] as $k) {
                if ($pushedRequest->getHeaderArray($k) !== $request->getHeaderArray($k)) {
                    continue 2;
                }
            }
            $response = yield $pushedResponse;
            foreach ($response->getHeaderArray('vary') as $vary) {
                foreach (preg_split('/\s*+,\s*+/', $vary) as $v) {
                    if ('*' === $v || $pushedRequest->getHeaderArray($v) !== $request->getHeaderArray($v) && 'accept-encoding' !== strtolower($v)) {
                        ($nullsafeVariable3 = $logger) ? $nullsafeVariable3->debug(sprintf('Skipping pushed response: "%s"', $info['url'])) : null;
                        continue 3;
                    }
                }
            }
            $pushDeferred->resolve();
            ($nullsafeVariable4 = $logger) ? $nullsafeVariable4->debug(sprintf('Accepting pushed response: "%s %s"', $info['http_method'], $info['url'])) : null;
            self::addResponseHeaders($response, $info, $headers);
            unset($multi->pushedResponses[$authority][$i]);
            if (!$multi->pushedResponses[$authority]) {
                unset($multi->pushedResponses[$authority]);
            }
            return $response;
        }
    }
    private static function stopLoop(): void
    {
        if (null !== self::$delay) {
            Loop::cancel(self::$delay);
            self::$delay = null;
        }
        Loop::defer(Closure::fromCallable([Loop::class, 'stop']));
    }
}
