<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use SRM_Redirect;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;

final class SafeRedirectManagerPlugin implements ModuleInterface
{
    public function hooks(): void
    {
        add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    public function setupIntegration(): void
    {
        if (!$this->isPluginActive()) {
            return;
        }
        add_filter('staatic_crawl_url_providers', [$this, 'registerCrawlUrlProvider'], 10, 2);
    }

    /**
     * @param CrawlUrlProviderCollection $providers
     * @param Publication $publication
     */
    public function registerCrawlUrlProvider($providers, $publication): CrawlUrlProviderCollection
    {
        $redirects = srm_get_redirects([
            'posts_per_page' => srm_get_max_redirects(),
            'post_status' => 'publish'
        ]);
        $additionalUrls = array_filter($redirects, function (array $item) {
            return $this->shouldInclude($item);
        });
        if (empty($additionalUrls)) {
            return $providers;
        }
        $additionalUrls = array_map(function (array $item) {
            return new AdditionalUrl($item['redirect_from']);
        }, $additionalUrls);
        $providers->addProvider(new AdditionalUrlCrawlUrlProvider($additionalUrls, $publication->build()->entryUrl()));

        return $providers;
    }

    private function shouldInclude(array $item): bool
    {
        if ($item['enable_regex']) {
            return \false;
        }
        if (strstr($item['redirect_from'], '*') !== \false) {
            return \false;
        }
        if (!in_array($item['status_code'], [301, 302, 307, 308])) {
            return \false;
        }

        return \true;
    }

    private function isPluginActive(): bool
    {
        if (!class_exists(SRM_Redirect::class)) {
            return \false;
        }
        if (!function_exists('Staatic\Vendor\srm_get_redirects')) {
            return \false;
        }
        if (!function_exists('Staatic\Vendor\srm_get_max_redirects')) {
            return \false;
        }

        return \true;
    }
}
