<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;

final class FlyingPressPlugin implements ModuleInterface
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
        add_filter('staatic_html_mapping_style', [$this, 'registerStyleAttributes']);
    }

    /**
     * @param mixed[] $attributes
     */
    public function registerStyleAttributes($attributes): array
    {
        return array_merge($attributes, ['data-lazy-style']);
    }

    private function isPluginActive(): bool
    {
        return defined('FLYING_PRESS_VERSION');
    }
}
