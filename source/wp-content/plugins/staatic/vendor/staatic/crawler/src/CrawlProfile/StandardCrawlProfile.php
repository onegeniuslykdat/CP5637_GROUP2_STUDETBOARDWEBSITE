<?php

namespace Staatic\Crawler\CrawlProfile;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\UrlEvaluator\InternalUrlEvaluator;
use Staatic\Crawler\UrlNormalizer\InternalUrlNormalizer;
use Staatic\Crawler\UrlTransformer\StandardUrlTransformer;
final class StandardCrawlProfile extends AbstractCrawlProfile
{
    public function __construct(UriInterface $baseUrl, UriInterface $destinationUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->destinationUrl = $destinationUrl;
        $this->urlEvaluator = new InternalUrlEvaluator($baseUrl);
        $this->urlNormalizer = new InternalUrlNormalizer();
        $this->urlTransformer = new StandardUrlTransformer($baseUrl, $destinationUrl);
    }
}
