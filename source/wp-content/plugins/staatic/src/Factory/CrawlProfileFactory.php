<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use RuntimeException;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\UrlEvaluator\CallbackUrlEvaluator;
use Staatic\Crawler\UrlEvaluator\ChainUrlEvaluator;
use Staatic\Crawler\UrlEvaluator\ExcludeRulesUrlEvaluator;
use Staatic\Crawler\UrlEvaluator\InternalUrlEvaluator;
use Staatic\Crawler\UrlEvaluator\UrlEvaluatorInterface;
use Staatic\WordPress\Bridge\CrawlProfile;
use Staatic\WordPress\Service\ExcludeUrls;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\Build\ExcludeUrlsSetting;
use Staatic\WordPress\Util\WordpressEnv;

final class CrawlProfileFactory
{
    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    /**
     * @var ExcludeUrlsSetting
     */
    private $excludeUrls;

    public function __construct(SiteUrlProvider $siteUrlProvider, ExcludeUrlsSetting $excludeUrls)
    {
        $this->siteUrlProvider = $siteUrlProvider;
        $this->excludeUrls = $excludeUrls;
    }

    public function __invoke(UriInterface $baseUrl, UriInterface $destinationUrl): CrawlProfileInterface
    {
        $lowercaseUrls = (bool) get_option('staatic_crawler_lowercase_urls');
        $urlEvaluator = $this->createUrlEvaluator($baseUrl);

        return new CrawlProfile($baseUrl, $destinationUrl, $lowercaseUrls, $urlEvaluator);
    }

    private function createUrlEvaluator(UriInterface $baseUrl): UrlEvaluatorInterface
    {
        $evaluators = [new InternalUrlEvaluator($baseUrl)];
        $excludeUrls = ExcludeUrls::resolve($this->excludeUrls->value(), ($this->siteUrlProvider)());
        $excludeUrls = $this->addBaseExcludeUrls($excludeUrls);
        $excludeUrls = apply_filters('staatic_exclude_urls', $excludeUrls, $baseUrl);
        if (count($excludeUrls)) {
            $evaluators[] = new ExcludeRulesUrlEvaluator($excludeUrls);
        }
        if (has_filter('staatic_should_crawl_url')) {
            $evaluators[] = new CallbackUrlEvaluator(function (UriInterface $resolvedUrl, array $context) {
                return (bool) apply_filters('staatic_should_crawl_url', \true, $resolvedUrl, $context);
            });
        }
        $evaluators = array_values(apply_filters('staatic_url_evaluators', $evaluators));
        if (empty($evaluators)) {
            throw new RuntimeException('No URL evaluators configured.');
        }

        return (count($evaluators) > 1) ? new ChainUrlEvaluator($evaluators) : $evaluators[0];
    }

    private function addBaseExcludeUrls(array $excludeUrls): array
    {
        $wordpressPrefix = rtrim(WordpressEnv::getWordpressUrlPath(), '/');
        $patterns = array_map(function ($pattern) use ($wordpressPrefix) {
            return sprintf($pattern, preg_quote($wordpressPrefix, '~'));
        }, ['~^%s/(xmlrpc|wp-comments-post|wp-login)\.php~', '~%s/wp-admin/?$~', '~/\?p=\d+~']);

        return array_merge($excludeUrls, $patterns);
    }
}
