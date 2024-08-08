<?php

namespace Staatic\Crawler\UrlTransformer;

use Closure;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class CallbackUrlTransformer implements UrlTransformerInterface
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
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transform($url, $foundOnUrl = null, $context = []): UrlTransformation
    {
        return ($this->callback)($url, $foundOnUrl, $context);
    }
}
