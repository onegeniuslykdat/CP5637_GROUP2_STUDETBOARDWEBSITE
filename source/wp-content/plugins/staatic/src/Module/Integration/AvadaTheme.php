<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;

final class AvadaTheme implements ModuleInterface
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
        $mapping['div'] = array_merge($mapping['div'], ['data-back', 'data-back-webp']);
        $mapping['link'] = array_merge($mapping['link'], ['imagesrcset', 'data-pmdelayedstyle']);

        return $mapping;
    }

    /**
     * @param mixed[] $attributes
     */
    public function registerSrcsetAttributes($attributes): array
    {
        return array_merge($attributes, ['imagesrcset']);
    }

    private function isPluginActive(): bool
    {
        return defined('AVADA_VERSION');
    }
}
