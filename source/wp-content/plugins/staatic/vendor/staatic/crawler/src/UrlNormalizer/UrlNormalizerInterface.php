<?php

namespace Staatic\Crawler\UrlNormalizer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface UrlNormalizerInterface
{
    /**
     * @param UriInterface $url
     */
    public function normalize($url): UriInterface;
}
