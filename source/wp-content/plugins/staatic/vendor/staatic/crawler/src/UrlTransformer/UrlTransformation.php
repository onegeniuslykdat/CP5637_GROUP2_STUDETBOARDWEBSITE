<?php

namespace Staatic\Crawler\UrlTransformer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class UrlTransformation
{
    /**
     * @var UriInterface
     */
    private $transformedUrl;
    /**
     * @var UriInterface|null
     */
    private $effectiveUrl;
    public function __construct(UriInterface $transformedUrl, ?UriInterface $effectiveUrl = null)
    {
        $this->transformedUrl = $transformedUrl;
        $this->effectiveUrl = $effectiveUrl;
    }
    public function transformedUrl(): UriInterface
    {
        return $this->transformedUrl;
    }
    public function effectiveUrl(): UriInterface
    {
        return $this->effectiveUrl ?: $this->transformedUrl;
    }
}
