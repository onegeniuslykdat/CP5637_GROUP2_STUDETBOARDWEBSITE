<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use RuntimeException;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\RequestFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\ServerRequestInterface;
use Staatic\Vendor\Psr\Http\Message\StreamFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\UploadedFileInterface;
use Staatic\Vendor\Psr\Http\Message\UriFactoryInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class HttpFactory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{
    /**
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function createUploadedFile($stream, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null): UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    /**
     * @param string $content
     */
    public function createStream($content = ''): StreamInterface
    {
        return Utils::streamFor($content);
    }
    /**
     * @param string $file
     * @param string $mode
     */
    public function createStreamFromFile($file, $mode = 'r'): StreamInterface
    {
        try {
            $resource = Utils::tryFopen($file, $mode);
        } catch (RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new InvalidArgumentException(sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource): StreamInterface
    {
        return Utils::streamFor($resource);
    }
    /**
     * @param string $method
     * @param mixed[] $serverParams
     */
    public function createServerRequest($method, $uri, $serverParams = []): ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    /**
     * @param int $code
     * @param string $reasonPhrase
     */
    public function createResponse($code = 200, $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
    /**
     * @param string $method
     */
    public function createRequest($method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
    /**
     * @param string $uri
     */
    public function createUri($uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
