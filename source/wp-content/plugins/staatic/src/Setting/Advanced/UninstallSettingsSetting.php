<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class UninstallSettingsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_uninstall_settings';
    }

    public function type(): string
    {
        return self::TYPE_BOOLEAN;
    }

    public function label(): string
    {
        return __('Delete Settings', 'staatic');
    }

    public function extendedLabel(): ?string
    {
        return __('Delete plugin settings', 'staatic');
    }

    public function description(): ?string
    {
        return __('This will delete all plugin\'s settings when uninstalling the plugin.', 'staatic');
    }

    public function defaultValue()
    {
        return \true;
    }
}
