<?php

namespace Staatic\Vendor\GuzzleHttp\Exception;

use Throwable;
use Staatic\Vendor\GuzzleHttp\BodySummarizer;
use Staatic\Vendor\GuzzleHttp\BodySummarizerInterface;
use Staatic\Vendor\Psr\Http\Client\RequestExceptionInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
class RequestException extends TransferException implements RequestExceptionInterface
{
    private $request;
    private $response;
    private $handlerContext;
    public function __construct(string $message, RequestInterface $request, ResponseInterface $response = null, Throwable $previous = null, array $handlerContext = [])
    {
        $code = $response ? $response->getStatusCode() : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
        $this->handlerContext = $handlerContext;
    }
    /**
     * @param RequestInterface $request
     * @param Throwable $e
     */
    public static function wrapException($request, $e): RequestException
    {
        return ($e instanceof RequestException) ? $e : new RequestException($e->getMessage(), $request, null, $e);
    }
    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @param Throwable|null $previous
     * @param mixed[] $handlerContext
     * @param BodySummarizerInterface|null $bodySummarizer
     */
    public static function create($request, $response = null, $previous = null, $handlerContext = [], $bodySummarizer = null): self
    {
        if (!$response) {
            return new self('Error completing request', $request, null, $previous, $handlerContext);
        }
        $level = (int) \floor($response->getStatusCode() / 100);
        if ($level === 4) {
            $label = 'Client error';
            $className = ClientException::class;
        } elseif ($level === 5) {
            $label = 'Server error';
            $className = ServerException::class;
        } else {
            $label = 'Unsuccessful request';
            $className = __CLASS__;
        }
        $uri = $request->getUri();
        $uri = static::obfuscateUri($uri);
        $message = \sprintf('%s: `%s %s` resulted in a `%s %s` response', $label, $request->getMethod(), $uri->__toString(), $response->getStatusCode(), $response->getReasonPhrase());
        $summary = ($bodySummarizer ?? new BodySummarizer())->summarize($response);
        if ($summary !== null) {
            $message .= ":\n{$summary}\n";
        }
        return new $className($message, $request, $response, $previous, $handlerContext);
    }
    private static function obfuscateUri(UriInterface $uri): UriInterface
    {
        $userInfo = $uri->getUserInfo();
        if (\false !== $pos = \strpos($userInfo, ':')) {
            return $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
        }
        return $uri;
    }
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
    public function getHandlerContext(): array
    {
        return $this->handlerContext;
    }
}
