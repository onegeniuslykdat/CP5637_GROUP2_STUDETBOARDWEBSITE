<?php

namespace Staatic\Crawler\UrlTransformer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface UrlTransformerInterface
{
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transform($url, $foundOnUrl = null, $context = []): UrlTransformation;
}
