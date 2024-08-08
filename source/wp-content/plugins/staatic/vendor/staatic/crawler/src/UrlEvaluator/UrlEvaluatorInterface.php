<?php

namespace Staatic\Crawler\UrlEvaluator;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface UrlEvaluatorInterface
{
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool;
}
