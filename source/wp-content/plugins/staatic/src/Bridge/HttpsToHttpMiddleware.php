<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Closure;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;

class HttpsToHttpMiddleware
{
    /**
     * @var array<string,mixed>
     */
    private $defaultOptions = [
        // Force http enabled. Toggle force http on or off per request.
        'force_http_enabled' => \true,
    ];

    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * Provides a closure that can be pushed onto the handler stack.
     *
     * Example:
     * <code>$handlerStack->push(HttpsToHttpMiddleware::factory());</code>
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
     * HttpsToHttpMiddleware constructor.
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
        // Combine options with defaults specified by this middleware.
        $options = array_replace($this->defaultOptions, $options);
        if ($options['force_http_enabled'] && $this->shouldUpdateRequest($request)) {
            $request = $this->updateRequest($request);
        }

        return ($this->nextHandler)($request, $options);
    }

    private function shouldUpdateRequest(RequestInterface $request): bool
    {
        return $request->getUri()->getScheme() === 'https';
    }

    private function updateRequest(RequestInterface $request): RequestInterface
    {
        return $request->withUri($request->getUri()->withScheme('http'));
    }
}
