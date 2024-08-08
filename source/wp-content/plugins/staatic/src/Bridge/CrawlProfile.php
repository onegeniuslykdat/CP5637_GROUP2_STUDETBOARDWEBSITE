<?php

namespace Staatic\WordPress\Bridge;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlProfile\AbstractCrawlProfile;
use Staatic\Crawler\UrlEvaluator\UrlEvaluatorInterface;
use Staatic\Crawler\UrlNormalizer\InternalUrlNormalizer;
use Staatic\Crawler\UrlTransformer\CallbackUrlTransformer;
use Staatic\Crawler\UrlTransformer\OfflineUrlTransformer;
use Staatic\Crawler\UrlTransformer\StandardUrlTransformer;
use Staatic\Crawler\UrlTransformer\UrlTransformation;

final class CrawlProfile extends AbstractCrawlProfile
{
    public function __construct(UriInterface $baseUrl, UriInterface $destinationUrl, bool $lowercaseUrls, UrlEvaluatorInterface $urlEvaluator)
    {
        $this->baseUrl = $baseUrl;
        $this->destinationUrl = $destinationUrl;
        $this->urlEvaluator = $urlEvaluator;
        $this->urlNormalizer = new InternalUrlNormalizer([
            'lowercase' => $lowercaseUrls
        ]);
        if ((string) $destinationUrl === '') {
            $this->urlTransformer = new OfflineUrlTransformer();
        } else {
            $this->urlTransformer = new StandardUrlTransformer($baseUrl, $destinationUrl);
        }
        if ($lowercaseUrls) {
            $originalTransformer = clone $this->urlTransformer;
            $this->urlTransformer = new CallbackUrlTransformer(function (
                UriInterface $url,
                ?UriInterface $foundOnUrl,
                array $context
            ) use (
                $originalTransformer
            ) {
                $transformation = $originalTransformer->transform($url, $foundOnUrl, $context);

                return new UrlTransformation(new Uri(mb_strtolower(
                    (string) $transformation->transformedUrl()
                )), new Uri(
                    mb_strtolower(
                    (string) $transformation->effectiveUrl()
                )
                ));
            });
        }
    }
}
