<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use BadMethodCallException;
use Generator;
use SplObjectStorage;
use TypeError;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\ErrorChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\ClientException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\RedirectionException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\ServerException;
use Staatic\Vendor\Symfony\Component\HttpClient\TraceableHttpClient;
use Staatic\Vendor\Symfony\Component\Stopwatch\StopwatchEvent;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
class TraceableResponse implements ResponseInterface, StreamableInterface
{
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var mixed
     */
    private $content;
    /**
     * @var \Staatic\Vendor\Symfony\Component\Stopwatch\StopwatchEvent|null
     */
    private $event;
    public function __construct(HttpClientInterface $client, ResponseInterface $response, &$content, StopwatchEvent $event = null)
    {
        $this->client = $client;
        $this->response = $response;
        $this->content =& $content;
        $this->event = $event;
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
            $this->response->__destruct();
        } finally {
            if (($nullsafeVariable1 = $this->event) ? $nullsafeVariable1->isStarted() : null) {
                $this->event->stop();
            }
        }
    }
    public function getStatusCode(): int
    {
        try {
            return $this->response->getStatusCode();
        } finally {
            if (($nullsafeVariable2 = $this->event) ? $nullsafeVariable2->isStarted() : null) {
                $this->event->lap();
            }
        }
    }
    /**
     * @param bool $throw
     */
    public function getHeaders($throw = \true): array
    {
        try {
            return $this->response->getHeaders($throw);
        } finally {
            if (($nullsafeVariable3 = $this->event) ? $nullsafeVariable3->isStarted() : null) {
                $this->event->lap();
            }
        }
    }
    /**
     * @param bool $throw
     */
    public function getContent($throw = \true): string
    {
        try {
            if (\false === $this->content) {
                return $this->response->getContent($throw);
            }
            return $this->content = $this->response->getContent(\false);
        } finally {
            if (($nullsafeVariable4 = $this->event) ? $nullsafeVariable4->isStarted() : null) {
                $this->event->stop();
            }
            if ($throw) {
                $this->checkStatusCode($this->response->getStatusCode());
            }
        }
    }
    /**
     * @param bool $throw
     */
    public function toArray($throw = \true): array
    {
        try {
            if (\false === $this->content) {
                return $this->response->toArray($throw);
            }
            return $this->content = $this->response->toArray(\false);
        } finally {
            if (($nullsafeVariable5 = $this->event) ? $nullsafeVariable5->isStarted() : null) {
                $this->event->stop();
            }
            if ($throw) {
                $this->checkStatusCode($this->response->getStatusCode());
            }
        }
    }
    public function cancel(): void
    {
        $this->response->cancel();
        if (($nullsafeVariable6 = $this->event) ? $nullsafeVariable6->isStarted() : null) {
            $this->event->stop();
        }
    }
    /**
     * @param string|null $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        return $this->response->getInfo($type);
    }
    /**
     * @param bool $throw
     */
    public function toStream($throw = \true)
    {
        if ($throw) {
            $this->response->getHeaders(\true);
        }
        if ($this->response instanceof StreamableInterface) {
            return $this->response->toStream(\false);
        }
        return StreamWrapper::createResource($this->response, $this->client);
    }
    /**
     * @param HttpClientInterface $client
     * @param iterable $responses
     * @param float|null $timeout
     */
    public static function stream($client, $responses, $timeout): Generator
    {
        $wrappedResponses = [];
        $traceableMap = new SplObjectStorage();
        foreach ($responses as $r) {
            if (!$r instanceof self) {
                throw new TypeError(sprintf('"%s::stream()" expects parameter 1 to be an iterable of TraceableResponse objects, "%s" given.', TraceableHttpClient::class, get_debug_type($r)));
            }
            $traceableMap[$r->response] = $r;
            $wrappedResponses[] = $r->response;
            if ($r->event && !$r->event->isStarted()) {
                $r->event->start();
            }
        }
        foreach ($client->stream($wrappedResponses, $timeout) as $r => $chunk) {
            if ($traceableMap[$r]->event && $traceableMap[$r]->event->isStarted()) {
                try {
                    if ($chunk->isTimeout() || !$chunk->isLast()) {
                        $traceableMap[$r]->event->lap();
                    } else {
                        $traceableMap[$r]->event->stop();
                    }
                } catch (TransportExceptionInterface $e) {
                    $traceableMap[$r]->event->stop();
                    if ($chunk instanceof ErrorChunk) {
                        $chunk->didThrow(\false);
                    } else {
                        $chunk = new ErrorChunk($chunk->getOffset(), $e);
                    }
                }
            }
            yield $traceableMap[$r] => $chunk;
        }
    }
    private function checkStatusCode(int $code): void
    {
        if (500 <= $code) {
            throw new ServerException($this);
        }
        if (400 <= $code) {
            throw new ClientException($this);
        }
        if (300 <= $code) {
            throw new RedirectionException($this);
        }
    }
}
