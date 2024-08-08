<?php

namespace Staatic\Crawler\UrlEvaluator;

use Closure;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class CallbackUrlEvaluator implements UrlEvaluatorInterface
{
    /**
     * @var Closure
     */
    private $callback;
    public function __construct(callable $callback)
    {
        $this->callback = Closure::fromCallable($callback);
    }
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool
    {
        return ($this->callback)($resolvedUrl, $context);
    }
}
