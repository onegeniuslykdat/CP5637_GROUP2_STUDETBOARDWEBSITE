<?php

namespace Staatic\Crawler\UrlExtractor;

use Generator;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface UrlExtractorInterface
{
    /**
     * @param string $content
     * @param UriInterface $baseUrl
     */
    public function extract($content, $baseUrl): Generator;
}
