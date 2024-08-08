<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

final class LoadTextDomain implements ModuleInterface
{
    public function hooks(): void
    {
        add_action('init', [$this, 'loadTextDomain'], 0);
    }

    public function loadTextDomain(): void
    {
        load_plugin_textdomain('staatic', \false, dirname(plugin_basename(\STAATIC_FILE)) . '/languages/');
    }

    public static function getDefaultPriority(): int
    {
        return 80;
    }
}
