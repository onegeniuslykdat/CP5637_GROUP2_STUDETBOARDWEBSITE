<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use WPSEO_Redirect_Manager;

final class YoastPremiumPlugin implements ModuleInterface
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
        $redirects = get_option('wpseo-premium-redirects-base');
        if (!is_array($redirects) || empty($redirects)) {
            return $providers;
        }
        $additionalUrls = array_filter($redirects, function (array $item) {
            return $this->shouldInclude($item);
        });
        if (empty($additionalUrls)) {
            return $providers;
        }
        $additionalUrls = array_map(function (array $item) {
            return new AdditionalUrl($item['origin']);
        }, $additionalUrls);
        $providers->addProvider(new AdditionalUrlCrawlUrlProvider($additionalUrls, $publication->build()->entryUrl()));

        return $providers;
    }

    private function shouldInclude(array $item): bool
    {
        if ($item['format'] !== 'plain') {
            return \false;
        }
        if (!in_array($item['type'], [301, 302, 307, 308])) {
            return \false;
        }

        return \true;
    }

    private function isPluginActive(): bool
    {
        return class_exists(WPSEO_Redirect_Manager::class);
    }
}
