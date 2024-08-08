<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\ServerSentEvent;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\EventSourceException;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncContext;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncResponse;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class EventSourceHttpClient implements HttpClientInterface, ResetInterface
{
    use AsyncDecoratorTrait, HttpClientTrait {
        AsyncDecoratorTrait::withOptions insteadof HttpClientTrait;
    }
    /**
     * @var float
     */
    private $reconnectionTime;
    public function __construct(HttpClientInterface $client = null, float $reconnectionTime = 10.0)
    {
        $this->client = $client ?? HttpClient::create();
        $this->reconnectionTime = $reconnectionTime;
    }
    /**
     * @param string $url
     * @param mixed[] $options
     */
    public function connect($url, $options = []): ResponseInterface
    {
        return $this->request('GET', $url, self::mergeDefaultOptions($options, ['buffer' => \false, 'headers' => ['Accept' => 'text/event-stream', 'Cache-Control' => 'no-cache']], \true));
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        $state = new class
        {
            /**
             * @var string|null
             */
            public $buffer;
            /**
             * @var string|null
             */
            public $lastEventId;
            /**
             * @var float
             */
            public $reconnectionTime;
            /**
             * @var float|null
             */
            public $lastError;
        };
        $state->reconnectionTime = $this->reconnectionTime;
        if ($accept = self::normalizeHeaders($options['headers'] ?? [])['accept'] ?? []) {
            $state->buffer = \in_array($accept, [['Accept: text/event-stream'], ['accept: text/event-stream']], \true) ? '' : null;
            if (null !== $state->buffer) {
                $options['extra']['trace_content'] = \false;
            }
        }
        return new AsyncResponse($this->client, $method, $url, $options, static function (ChunkInterface $chunk, AsyncContext $context) use ($state, $method, $url, $options) {
            if (null !== $state->buffer) {
                $context->setInfo('reconnection_time', $state->reconnectionTime);
                $isTimeout = \false;
            }
            $lastError = $state->lastError;
            $state->lastError = null;
            try {
                $isTimeout = $chunk->isTimeout();
                if (null !== $chunk->getInformationalStatus() || $context->getInfo('canceled')) {
                    yield $chunk;
                    return;
                }
            } catch (TransportExceptionInterface $exception) {
                $state->lastError = $lastError ?? microtime(\true);
                if (null === $state->buffer || $isTimeout && microtime(\true) - $state->lastError < $state->reconnectionTime) {
                    yield $chunk;
                } else {
                    $options['headers']['Last-Event-ID'] = $state->lastEventId;
                    $state->buffer = '';
                    $state->lastError = microtime(\true);
                    $context->getResponse()->cancel();
                    $context->replaceRequest($method, $url, $options);
                    if ($isTimeout) {
                        yield $chunk;
                    } else {
                        $context->pause($state->reconnectionTime);
                    }
                }
                return;
            }
            if ($chunk->isFirst()) {
                if (preg_match('/^text\/event-stream(;|$)/i', $context->getHeaders()['content-type'][0] ?? '')) {
                    $state->buffer = '';
                } elseif (null !== $lastError || null !== $state->buffer && 200 === $context->getStatusCode()) {
                    throw new EventSourceException(sprintf('Response content-type is "%s" while "text/event-stream" was expected for "%s".', $context->getHeaders()['content-type'][0] ?? '', $context->getInfo('url')));
                } else {
                    $context->passthru();
                }
                if (null === $lastError) {
                    yield $chunk;
                }
                return;
            }
            $rx = '/((?:\r\n|[\r\n]){2,})/';
            $content = $state->buffer . $chunk->getContent();
            if ($chunk->isLast()) {
                $rx = substr_replace($rx, '|$', -2, 0);
            }
            $events = preg_split($rx, $content, -1, \PREG_SPLIT_DELIM_CAPTURE);
            $state->buffer = array_pop($events);
            for ($i = 0; isset($events[$i]); $i += 2) {
                $event = new ServerSentEvent($events[$i] . $events[1 + $i]);
                if ('' !== $event->getId()) {
                    $context->setInfo('last_event_id', $state->lastEventId = $event->getId());
                }
                if ($event->getRetry()) {
                    $context->setInfo('reconnection_time', $state->reconnectionTime = $event->getRetry());
                }
                yield $event;
            }
            if (preg_match('/^(?::[^\r\n]*+(?:\r\n|[\r\n]))+$/m', $state->buffer)) {
                $content = $state->buffer;
                $state->buffer = '';
                yield $context->createChunk($content);
            }
            if ($chunk->isLast()) {
                yield $chunk;
            }
        });
    }
}
