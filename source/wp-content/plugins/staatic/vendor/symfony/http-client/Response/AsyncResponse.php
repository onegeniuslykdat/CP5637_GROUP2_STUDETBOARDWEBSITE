<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Generator;
use SplObjectStorage;
use TypeError;
use LogicException;
use Iterator;
use Throwable;
use Closure;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\ErrorChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\FirstChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\LastChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class AsyncResponse implements ResponseInterface, StreamableInterface
{
    use CommonResponseTrait;
    private const FIRST_CHUNK_YIELDED = 1;
    private const LAST_CHUNK_YIELDED = 2;
    /**
     * @var HttpClientInterface|null
     */
    private $client;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var mixed[]
     */
    private $info = ['canceled' => \false];
    private $passthru;
    private $stream;
    private $yieldedState;
    public function __construct(HttpClientInterface $client, string $method, string $url, array $options, callable $passthru = null)
    {
        $this->client = $client;
        $this->shouldBuffer = $options['buffer'] ?? \true;
        if (null !== $onProgress = $options['on_progress'] ?? null) {
            $thisInfo =& $this->info;
            $options['on_progress'] = static function (int $dlNow, int $dlSize, array $info) use (&$thisInfo, $onProgress) {
                $onProgress($dlNow, $dlSize, $thisInfo + $info);
            };
        }
        $this->response = $client->request($method, $url, ['buffer' => \false] + $options);
        $this->passthru = $passthru;
        $this->initializer = static function (self $response, float $timeout = null) {
            if (null === $response->shouldBuffer) {
                return \false;
            }
            while (\true) {
                foreach (self::stream([$response], $timeout) as $chunk) {
                    if ($chunk->isTimeout() && $response->passthru) {
                        foreach (self::passthru($response->client, $response, new ErrorChunk($response->offset, new TransportException($chunk->getError()))) as $chunk) {
                            if ($chunk->isFirst()) {
                                return \false;
                            }
                        }
                        continue 2;
                    }
                    if ($chunk->isFirst()) {
                        return \false;
                    }
                }
                return \false;
            }
        };
        if (\array_key_exists('user_data', $options)) {
            $this->info['user_data'] = $options['user_data'];
        }
        if (\array_key_exists('max_duration', $options)) {
            $this->info['max_duration'] = $options['max_duration'];
        }
    }
    public function getStatusCode(): int
    {
        if ($this->initializer) {
            self::initialize($this);
        }
        return $this->response->getStatusCode();
    }
    /**
     * @param bool $throw
     */
    public function getHeaders($throw = \true): array
    {
        if ($this->initializer) {
            self::initialize($this);
        }
        $headers = $this->response->getHeaders(\false);
        if ($throw) {
            $this->checkStatusCode();
        }
        return $headers;
    }
    /**
     * @param string|null $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        if (null !== $type) {
            return $this->info[$type] ?? $this->response->getInfo($type);
        }
        return $this->info + $this->response->getInfo();
    }
    /**
     * @param bool $throw
     */
    public function toStream($throw = \true)
    {
        if ($throw) {
            $this->getHeaders(\true);
        }
        $handle = function () {
            $stream = ($this->response instanceof StreamableInterface) ? $this->response->toStream(\false) : StreamWrapper::createResource($this->response);
            return stream_get_meta_data($stream)['wrapper_data']->stream_cast(\STREAM_CAST_FOR_SELECT);
        };
        $stream = StreamWrapper::createResource($this);
        stream_get_meta_data($stream)['wrapper_data']->bindHandles($handle, $this->content);
        return $stream;
    }
    public function cancel(): void
    {
        if ($this->info['canceled']) {
            return;
        }
        $this->info['canceled'] = \true;
        $this->info['error'] = 'Response has been canceled.';
        $this->close();
        $client = $this->client;
        $this->client = null;
        if (!$this->passthru) {
            return;
        }
        try {
            foreach (self::passthru($client, $this, new LastChunk()) as $chunk) {
            }
            $this->passthru = null;
        } catch (ExceptionInterface $exception) {
        }
    }
    public function __destruct()
    {
        $httpException = null;
        if ($this->initializer && null === $this->getInfo('error')) {
            try {
                self::initialize($this, -0.0);
                $this->getHeaders(\true);
            } catch (HttpExceptionInterface $httpException) {
            }
        }
        if ($this->passthru && null === $this->getInfo('error')) {
            $this->info['canceled'] = \true;
            try {
                foreach (self::passthru($this->client, $this, new LastChunk()) as $chunk) {
                }
            } catch (ExceptionInterface $exception) {
            }
        }
        if (null !== $httpException) {
            throw $httpException;
        }
    }
    /**
     * @param iterable $responses
     * @param float|null $timeout
     * @param string|null $class
     */
    public static function stream($responses, $timeout = null, $class = null): Generator
    {
        while ($responses) {
            $wrappedResponses = [];
            $asyncMap = new SplObjectStorage();
            $client = null;
            foreach ($responses as $r) {
                if (!$r instanceof self) {
                    throw new TypeError(sprintf('"%s::stream()" expects parameter 1 to be an iterable of AsyncResponse objects, "%s" given.', $class ?? static::class, get_debug_type($r)));
                }
                if (null !== $e = $r->info['error'] ?? null) {
                    yield $r => $chunk = new ErrorChunk($r->offset, new TransportException($e));
                    $chunk->didThrow() ?: $chunk->getContent();
                    continue;
                }
                if (null === $client) {
                    $client = $r->client;
                } elseif ($r->client !== $client) {
                    throw new TransportException('Cannot stream AsyncResponse objects with many clients.');
                }
                $asyncMap[$r->response] = $r;
                $wrappedResponses[] = $r->response;
                if ($r->stream) {
                    yield from self::passthruStream($response = $r->response, $r, new FirstChunk(), $asyncMap);
                    if (!isset($asyncMap[$response])) {
                        array_pop($wrappedResponses);
                    }
                    if ($r->response !== $response && !isset($asyncMap[$r->response])) {
                        $asyncMap[$r->response] = $r;
                        $wrappedResponses[] = $r->response;
                    }
                }
            }
            if (!$client || !$wrappedResponses) {
                return;
            }
            foreach ($client->stream($wrappedResponses, $timeout) as $response => $chunk) {
                $r = $asyncMap[$response];
                if (null === $chunk->getError()) {
                    if ($chunk->isFirst()) {
                        $r->response->getStatusCode();
                    } elseif (0 === $r->offset && null === $r->content && $chunk->isLast()) {
                        $r->content = fopen('php://memory', 'w+');
                    }
                }
                if (!$r->passthru) {
                    if (null !== $chunk->getError() || $chunk->isLast()) {
                        unset($asyncMap[$response]);
                    } elseif (null !== $r->content && '' !== ($content = $chunk->getContent()) && \strlen($content) !== fwrite($r->content, $content)) {
                        $chunk = new ErrorChunk($r->offset, new TransportException(sprintf('Failed writing %d bytes to the response buffer.', \strlen($content))));
                        $r->info['error'] = $chunk->getError();
                        $r->response->cancel();
                    }
                    yield $r => $chunk;
                    continue;
                }
                if (null !== $chunk->getError()) {
                } elseif ($chunk->isFirst()) {
                    $r->yieldedState = self::FIRST_CHUNK_YIELDED;
                } elseif (self::FIRST_CHUNK_YIELDED !== $r->yieldedState && null === $chunk->getInformationalStatus()) {
                    throw new LogicException(sprintf('Instance of "%s" is already consumed and cannot be managed by "%s". A decorated client should not call any of the response\'s methods in its "request()" method.', get_debug_type($response), $class ?? static::class));
                }
                foreach (self::passthru($r->client, $r, $chunk, $asyncMap) as $chunk) {
                    yield $r => $chunk;
                }
                if ($r->response !== $response && isset($asyncMap[$response])) {
                    break;
                }
            }
            if (null === $chunk->getError() && $chunk->isLast()) {
                $r->yieldedState = self::LAST_CHUNK_YIELDED;
            }
            if (null === $chunk->getError() && self::LAST_CHUNK_YIELDED !== $r->yieldedState && $r->response === $response && null !== $r->client) {
                throw new LogicException('A chunk passthru must yield an "isLast()" chunk before ending a stream.');
            }
            $responses = [];
            foreach ($asyncMap as $response) {
                $r = $asyncMap[$response];
                if (null !== $r->client) {
                    $responses[] = $asyncMap[$response];
                }
            }
        }
    }
    private static function passthru(HttpClientInterface $client, self $r, ChunkInterface $chunk, SplObjectStorage $asyncMap = null): Generator
    {
        $r->stream = null;
        $response = $r->response;
        $context = new AsyncContext($r->passthru, $client, $r->response, $r->info, $r->content, $r->offset);
        if (null === $stream = ($r->passthru)($chunk, $context)) {
            if ($r->response === $response && (null !== $chunk->getError() || $chunk->isLast())) {
                throw new LogicException('A chunk passthru cannot swallow the last chunk.');
            }
            return;
        }
        if (!$stream instanceof Iterator) {
            throw new LogicException(sprintf('A chunk passthru must return an "Iterator", "%s" returned.', get_debug_type($stream)));
        }
        $r->stream = $stream;
        yield from self::passthruStream($response, $r, null, $asyncMap);
    }
    private static function passthruStream(ResponseInterface $response, self $r, ?ChunkInterface $chunk, ?SplObjectStorage $asyncMap): Generator
    {
        while (\true) {
            try {
                if (null !== $chunk && $r->stream) {
                    $r->stream->next();
                }
                if (!$r->stream || !$r->stream->valid() || !$r->stream) {
                    $r->stream = null;
                    break;
                }
            } catch (Throwable $e) {
                unset($asyncMap[$response]);
                $r->stream = null;
                $r->info['error'] = $e->getMessage();
                $r->response->cancel();
                yield $r => $chunk = new ErrorChunk($r->offset, $e);
                $chunk->didThrow() ?: $chunk->getContent();
                break;
            }
            $chunk = $r->stream->current();
            if (!$chunk instanceof ChunkInterface) {
                throw new LogicException(sprintf('A chunk passthru must yield instances of "%s", "%s" yielded.', ChunkInterface::class, get_debug_type($chunk)));
            }
            if (null !== $chunk->getError()) {
            } elseif ($chunk->isFirst()) {
                $e = $r->openBuffer();
                yield $r => $chunk;
                if ($r->initializer && null === $r->getInfo('error')) {
                    $r->getHeaders(\true);
                }
                if (null === $e) {
                    continue;
                }
                $r->response->cancel();
                $chunk = new ErrorChunk($r->offset, $e);
            } elseif ('' !== $content = $chunk->getContent()) {
                if (null !== $r->shouldBuffer) {
                    throw new LogicException('A chunk passthru must yield an "isFirst()" chunk before any content chunk.');
                }
                if (null !== $r->content && \strlen($content) !== fwrite($r->content, $content)) {
                    $chunk = new ErrorChunk($r->offset, new TransportException(sprintf('Failed writing %d bytes to the response buffer.', \strlen($content))));
                    $r->info['error'] = $chunk->getError();
                    $r->response->cancel();
                }
            }
            if (null !== $chunk->getError() || $chunk->isLast()) {
                $stream = $r->stream;
                $r->stream = null;
                unset($asyncMap[$response]);
            }
            if (null === $chunk->getError()) {
                $r->offset += \strlen($content);
                yield $r => $chunk;
                if (!$chunk->isLast()) {
                    continue;
                }
                $stream->next();
                if ($stream->valid()) {
                    throw new LogicException('A chunk passthru cannot yield after an "isLast()" chunk.');
                }
                $r->passthru = null;
            } else {
                if ($chunk instanceof ErrorChunk) {
                    $chunk->didThrow(\false);
                } else {
                    try {
                        $chunk = new ErrorChunk($chunk->getOffset(), (!$chunk->isTimeout()) ?: $chunk->getError());
                    } catch (TransportExceptionInterface $e) {
                        $chunk = new ErrorChunk($chunk->getOffset(), $e);
                    }
                }
                yield $r => $chunk;
                $chunk->didThrow() ?: $chunk->getContent();
            }
            break;
        }
    }
    private function openBuffer(): ?Throwable
    {
        if (null === $shouldBuffer = $this->shouldBuffer) {
            throw new LogicException('A chunk passthru cannot yield more than one "isFirst()" chunk.');
        }
        $e = $this->shouldBuffer = null;
        if ($shouldBuffer instanceof Closure) {
            try {
                $shouldBuffer = $shouldBuffer($this->getHeaders(\false));
                if (null !== $e = $this->response->getInfo('error')) {
                    throw new TransportException($e);
                }
            } catch (Throwable $e) {
                $this->info['error'] = $e->getMessage();
                $this->response->cancel();
            }
        }
        if (\true === $shouldBuffer) {
            $this->content = fopen('php://temp', 'w+');
        } elseif (\is_resource($shouldBuffer)) {
            $this->content = $shouldBuffer;
        }
        return $e;
    }
    private function close(): void
    {
        $this->response->cancel();
    }
}
