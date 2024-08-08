<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core;

use Staatic\Vendor\AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use Staatic\Vendor\AsyncAws\Core\AwsError\ChainAwsErrorFactory;
use Staatic\Vendor\AsyncAws\Core\EndpointDiscovery\EndpointCache;
use Staatic\Vendor\AsyncAws\Core\Exception\Exception;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\ClientException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\HttpException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\NetworkException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\RedirectionException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\ServerException;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Exception\LogicException;
use Staatic\Vendor\AsyncAws\Core\Exception\RuntimeException;
use Staatic\Vendor\AsyncAws\Core\Exception\UnparsableResponse;
use Staatic\Vendor\AsyncAws\Core\Stream\ResponseBodyResourceStream;
use Staatic\Vendor\AsyncAws\Core\Stream\ResponseBodyStream;
use Staatic\Vendor\AsyncAws\Core\Stream\ResultStream;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class Response
{
    private $httpResponse;
    private $httpClient;
    private $resolveResult;
    private $bodyDownloaded = \false;
    private $streamStarted = \false;
    private $didThrow = \false;
    private $logger;
    private $awsErrorFactory;
    private $endpointCache;
    private $request;
    private $debug;
    private $exceptionMapping;
    public function __construct(ResponseInterface $response, HttpClientInterface $httpClient, LoggerInterface $logger, ?AwsErrorFactoryInterface $awsErrorFactory = null, ?EndpointCache $endpointCache = null, ?Request $request = null, bool $debug = \false, array $exceptionMapping = [])
    {
        $this->httpResponse = $response;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->awsErrorFactory = $awsErrorFactory ?? new ChainAwsErrorFactory();
        $this->endpointCache = $endpointCache;
        $this->request = $request;
        $this->debug = $debug;
        $this->exceptionMapping = $exceptionMapping;
    }
    public function __destruct()
    {
        if (null === $this->resolveResult || !$this->didThrow) {
            $this->resolve();
        }
    }
    public function resolve(?float $timeout = null): bool
    {
        if (null !== $this->resolveResult) {
            return $this->getResolveStatus();
        }
        try {
            if (null === $timeout) {
                $this->httpResponse->getStatusCode();
            } else {
                foreach ($this->httpClient->stream($this->httpResponse, $timeout) as $chunk) {
                    if ($chunk->isTimeout()) {
                        return \false;
                    }
                    if ($chunk->isFirst()) {
                        break;
                    }
                }
            }
            $this->defineResolveStatus();
        } catch (TransportExceptionInterface $e) {
            $this->resolveResult = new NetworkException('Could not contact remote server.', 0, $e);
        }
        if (\true === $this->debug) {
            $httpStatusCode = $this->httpResponse->getInfo('http_code');
            if (0 === $httpStatusCode) {
                $this->logger->debug('AsyncAws HTTP request could not be sent due network issues');
            } else {
                $this->logger->debug('AsyncAws HTTP response received with status code {status_code}', ['status_code' => $httpStatusCode, 'headers' => json_encode($this->httpResponse->getHeaders(\false)), 'body' => $this->httpResponse->getContent(\false)]);
                $this->bodyDownloaded = \true;
            }
        }
        return $this->getResolveStatus();
    }
    final public static function wait(iterable $responses, ?float $timeout = null, bool $downloadBody = \false): iterable
    {
        $responseMap = [];
        $indexMap = [];
        $httpResponses = [];
        $httpClient = null;
        foreach ($responses as $index => $response) {
            if (null !== $response->resolveResult && (\true !== $response->resolveResult || !$downloadBody || $response->bodyDownloaded)) {
                yield $index => $response;
                continue;
            }
            if (null === $httpClient) {
                $httpClient = $response->httpClient;
            } elseif ($httpClient !== $response->httpClient) {
                throw new LogicException('Unable to wait for the given results, they all have to be created with the same HttpClient');
            }
            $httpResponses[] = $response->httpResponse;
            $indexMap[$hash = spl_object_id($response->httpResponse)] = $index;
            $responseMap[$hash] = $response;
        }
        if (empty($httpResponses)) {
            return;
        }
        if (null === $httpClient) {
            throw new InvalidArgument('At least one response should have contain an Http Client');
        }
        foreach ($httpClient->stream($httpResponses, $timeout) as $httpResponse => $chunk) {
            $hash = spl_object_id($httpResponse);
            $response = $responseMap[$hash] ?? null;
            if (null === $response) {
                continue;
            }
            $index = $indexMap[$hash] ?? null;
            try {
                if ($chunk->isTimeout()) {
                    break;
                }
            } catch (TransportExceptionInterface $e) {
                $response->resolveResult = new NetworkException('Could not contact remote server.', 0, $e);
                if (null !== $index) {
                    unset($indexMap[$hash]);
                    yield $index => $response;
                    if (empty($indexMap)) {
                        return;
                    }
                }
            }
            if (!$response->streamStarted && '' !== $chunk->getContent()) {
                $response->streamStarted = \true;
            }
            if ($chunk->isLast()) {
                $response->bodyDownloaded = \true;
                if (null !== $index && $downloadBody) {
                    unset($indexMap[$hash]);
                    yield $index => $response;
                }
            }
            if ($chunk->isFirst()) {
                $response->defineResolveStatus();
                if (null !== $index && !$downloadBody) {
                    unset($indexMap[$hash]);
                    yield $index => $response;
                }
            }
            if (empty($indexMap)) {
                return;
            }
        }
    }
    public function info(): array
    {
        return ['resolved' => null !== $this->resolveResult, 'body_downloaded' => $this->bodyDownloaded, 'response' => $this->httpResponse, 'status' => (int) $this->httpResponse->getInfo('http_code')];
    }
    public function cancel(): void
    {
        $this->httpResponse->cancel();
        $this->resolveResult = \false;
    }
    public function getHeaders(): array
    {
        $this->resolve();
        return $this->httpResponse->getHeaders(\false);
    }
    public function getContent(): string
    {
        $this->resolve();
        try {
            return $this->httpResponse->getContent(\false);
        } finally {
            $this->bodyDownloaded = \true;
        }
    }
    public function toArray(): array
    {
        $this->resolve();
        try {
            return $this->httpResponse->toArray(\false);
        } catch (DecodingExceptionInterface $e) {
            throw new UnparsableResponse('Could not parse response as array', 0, $e);
        } finally {
            $this->bodyDownloaded = \true;
        }
    }
    public function getStatusCode(): int
    {
        return $this->httpResponse->getStatusCode();
    }
    public function toStream(): ResultStream
    {
        $this->resolve();
        if (\is_callable([$this->httpResponse, 'toStream'])) {
            return new ResponseBodyResourceStream($this->httpResponse->toStream());
        }
        if ($this->streamStarted) {
            throw new RuntimeException('Can not create a ResultStream because the body started being downloaded. The body was started to be downloaded in Response::wait()');
        }
        try {
            return new ResponseBodyStream($this->httpClient->stream($this->httpResponse));
        } finally {
            $this->bodyDownloaded = \true;
        }
    }
    private function defineResolveStatus(): void
    {
        try {
            $statusCode = $this->httpResponse->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            $this->resolveResult = static function () use ($e): NetworkException {
                return new NetworkException('Could not contact remote server.', 0, $e);
            };
            return;
        }
        if (300 <= $statusCode) {
            try {
                $awsError = $this->awsErrorFactory->createFromResponse($this->httpResponse);
                if ($this->request && $this->endpointCache && (400 === $statusCode || 'InvalidEndpointException' === $awsError->getCode())) {
                    $this->endpointCache->removeEndpoint($this->request->getEndpoint());
                }
            } catch (UnparsableResponse $e) {
                $awsError = null;
            }
            if (null !== ($awsCode = $awsError ? $awsError->getCode() : null) && isset($this->exceptionMapping[$awsCode])) {
                $exceptionClass = $this->exceptionMapping[$awsCode];
            } elseif (isset($this->exceptionMapping['http_status_code_' . $statusCode])) {
                $exceptionClass = $this->exceptionMapping['http_status_code_' . $statusCode];
            } elseif (500 <= $statusCode) {
                $exceptionClass = ServerException::class;
            } elseif (400 <= $statusCode) {
                $exceptionClass = ClientException::class;
            } else {
                $exceptionClass = RedirectionException::class;
            }
            $httpResponse = $this->httpResponse;
            $this->resolveResult = static function () use ($exceptionClass, $httpResponse, $awsError): HttpException {
                return new $exceptionClass($httpResponse, $awsError);
            };
            return;
        }
        $this->resolveResult = \true;
    }
    private function getResolveStatus(): bool
    {
        if (\is_bool($this->resolveResult)) {
            return $this->resolveResult;
        }
        if (\is_callable($this->resolveResult)) {
            $this->resolveResult = ($this->resolveResult)();
        }
        $code = null;
        $message = null;
        $context = ['exception' => $this->resolveResult];
        if ($this->resolveResult instanceof HttpException) {
            $code = $this->httpResponse->getInfo('http_code');
            $url = $this->httpResponse->getInfo('url');
            $context['aws_code'] = $this->resolveResult->getAwsCode();
            $context['aws_message'] = $this->resolveResult->getAwsMessage();
            $context['aws_type'] = $this->resolveResult->getAwsType();
            $context['aws_detail'] = $this->resolveResult->getAwsDetail();
            $message = sprintf('HTTP %d returned for "%s".', $code, $url);
        }
        if ($this->resolveResult instanceof Exception) {
            $this->logger->log((404 === $code) ? LogLevel::INFO : LogLevel::ERROR, $message ?? $this->resolveResult->getMessage(), $context);
            $this->didThrow = \true;
            throw $this->resolveResult;
        }
        throw new RuntimeException('Unexpected resolve state');
    }
}
