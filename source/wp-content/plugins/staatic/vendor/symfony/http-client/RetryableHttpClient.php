<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use Generator;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncContext;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Staatic\Vendor\Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
class RetryableHttpClient implements HttpClientInterface, ResetInterface
{
    use AsyncDecoratorTrait;
    /**
     * @var RetryStrategyInterface
     */
    private $strategy;
    /**
     * @var int
     */
    private $maxRetries;
    /**
     * @var LoggerInterface
     */
    private $logger;
    public function __construct(HttpClientInterface $client, RetryStrategyInterface $strategy = null, int $maxRetries = 3, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->strategy = $strategy ?? new GenericRetryStrategy();
        $this->maxRetries = $maxRetries;
        $this->logger = $logger ?? new NullLogger();
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        if ($this->maxRetries <= 0) {
            return new AsyncResponse($this->client, $method, $url, $options);
        }
        $retryCount = 0;
        $content = '';
        $firstChunk = null;
        return new AsyncResponse($this->client, $method, $url, $options, function (ChunkInterface $chunk, AsyncContext $context) use ($method, $url, $options, &$retryCount, &$content, &$firstChunk) {
            $exception = null;
            try {
                if ($context->getInfo('canceled') || $chunk->isTimeout() || null !== $chunk->getInformationalStatus()) {
                    yield $chunk;
                    return;
                }
            } catch (TransportExceptionInterface $exception) {
            }
            if (null !== $exception) {
                if ('' !== $context->getInfo('primary_ip')) {
                    $shouldRetry = $this->strategy->shouldRetry($context, null, $exception);
                    if (null === $shouldRetry) {
                        throw new LogicException(sprintf('The "%s::shouldRetry()" method must not return null when called with an exception.', \get_class($this->strategy)));
                    }
                    if (\false === $shouldRetry) {
                        yield from $this->passthru($context, $firstChunk, $content, $chunk);
                        return;
                    }
                }
            } elseif ($chunk->isFirst()) {
                if (\false === $shouldRetry = $this->strategy->shouldRetry($context, null, null)) {
                    yield from $this->passthru($context, $firstChunk, $content, $chunk);
                    return;
                }
                if (null === $shouldRetry) {
                    $firstChunk = $chunk;
                    $content = '';
                    return;
                }
            } else {
                if (!$chunk->isLast()) {
                    $content .= $chunk->getContent();
                    return;
                }
                if (null === $shouldRetry = $this->strategy->shouldRetry($context, $content, null)) {
                    throw new LogicException(sprintf('The "%s::shouldRetry()" method must not return null when called with a body.', \get_class($this->strategy)));
                }
                if (\false === $shouldRetry) {
                    yield from $this->passthru($context, $firstChunk, $content, $chunk);
                    return;
                }
            }
            $context->getResponse()->cancel();
            $delay = $this->getDelayFromHeader($context->getHeaders()) ?? $this->strategy->getDelay($context, (!$exception && $chunk->isLast()) ? $content : null, $exception);
            ++$retryCount;
            $content = '';
            $firstChunk = null;
            $this->logger->info('Try #{count} after {delay}ms' . ($exception ? ': ' . $exception->getMessage() : (', status code: ' . $context->getStatusCode())), ['count' => $retryCount, 'delay' => $delay]);
            $context->setInfo('retry_count', $retryCount);
            $context->replaceRequest($method, $url, $options);
            $context->pause($delay / 1000);
            if ($retryCount >= $this->maxRetries) {
                $context->passthru();
            }
        });
    }
    private function getDelayFromHeader(array $headers): ?int
    {
        if (null !== $after = $headers['retry-after'][0] ?? null) {
            if (is_numeric($after)) {
                return (int) ($after * 1000);
            }
            if (\false !== $time = strtotime($after)) {
                return max(0, $time - time()) * 1000;
            }
        }
        return null;
    }
    private function passthru(AsyncContext $context, ?ChunkInterface $firstChunk, string &$content, ChunkInterface $lastChunk): Generator
    {
        $context->passthru();
        if (null !== $firstChunk) {
            yield $firstChunk;
        }
        if ('' !== $content) {
            $chunk = $context->createChunk($content);
            $content = '';
            yield $chunk;
        }
        yield $lastChunk;
    }
}
