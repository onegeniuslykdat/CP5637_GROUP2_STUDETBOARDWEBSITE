<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;

final class WpFastestCachePlugin implements ModuleInterface
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
        add_filter('staatic_html_mapping_tags', [$this, 'registerMappingTags']);
        add_filter('staatic_html_mapping_srcset', [$this, 'registerSrcsetAttributes']);
    }

    /**
     * @param mixed[] $mapping
     */
    public function registerMappingTags($mapping): array
    {
        $mapping['img'] = array_merge($mapping['img'], ['data-wpfc-original-srcset']);
        $mapping['amp-img'] = array_merge($mapping['amp-img'], ['data-wpfc-original-srcset']);
        $mapping['source'] = array_merge($mapping['source'], ['data-wpfc-original-srcset']);

        return $mapping;
    }

    /**
     * @param mixed[] $attributes
     */
    public function registerSrcsetAttributes($attributes): array
    {
        return array_merge($attributes, ['data-wpfc-original-srcset']);
    }

    private function isPluginActive(): bool
    {
        return defined('WPFC_MAIN_PATH');
    }
}
