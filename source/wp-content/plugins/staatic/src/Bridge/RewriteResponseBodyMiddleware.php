<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Closure;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;

class RewriteResponseBodyMiddleware
{
    private const MAX_BODY_SIZE = 1024 * 1024 * 16;

    /**
     * @var array<string,mixed>
     */
    private $defaultOptions = [
        'rewrite_response_body_enabled' => \true,
        'replacements' => []
    ];

    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * Provides a closure that can be pushed onto the handler stack.
     *
     * Example:
     * <code>$handlerStack->push(RewriteResponseBodyMiddleware::factory());</code>
     *
     * @param array<string,mixed> $defaultOptions
     * @return Closure
     */
    public static function factory($defaultOptions = []): Closure
    {
        return function (callable $handler) use ($defaultOptions): self {
            return new static($handler, $defaultOptions);
        };
    }

    /**
     * RewriteResponseBodyMiddleware constructor.
     *
     * @param callable $nextHandler
     * @param array<string,mixed> $defaultOptions
     */
    final public function __construct(callable $nextHandler, array $defaultOptions = [])
    {
        $this->nextHandler = $nextHandler;
        $this->defaultOptions = array_replace($this->defaultOptions, $defaultOptions);
    }

    /**
     * @param RequestInterface $request
     * @param array<string,mixed> $options
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $options = array_replace($this->defaultOptions, $options);
        /** @var PromiseInterface $promise */
        $promise = ($this->nextHandler)($request, $options);

        return $promise->then(function (ResponseInterface $response) use ($request, $options) {
            if ($options['rewrite_response_body_enabled'] && !empty($options['replacements']) && $this->shouldRewriteResponse(
                $response
            )) {
                return $this->rewriteResponse($response, $options);
            }

            return $response;
        });
    }

    private function shouldRewriteResponse(ResponseInterface $response): bool
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $size = $response->getBody()->getSize();

        return stripos($contentType, 'text/html') !== \false && $size !== null && $size <= self::MAX_BODY_SIZE;
    }

    private function rewriteResponse(ResponseInterface $response, array $options): ResponseInterface
    {
        $newBody = Utils::streamFor(strtr($response->getBody()->getContents(), $options['replacements']));

        return $response->withBody($newBody);
    }
}
