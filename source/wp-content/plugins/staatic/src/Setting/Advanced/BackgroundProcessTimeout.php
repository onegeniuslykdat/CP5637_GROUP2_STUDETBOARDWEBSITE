<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class BackgroundProcessTimeout extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_background_process_timeout';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('Publication Task Timeout', 'staatic');
    }

    public function description(): ?string
    {
        return __('The number of seconds before publication tasks triggered from WP-Admin timeout.<br>Only change this value to troubleshoot publication issues. Alternatively use the <code>staatic publish</code> WP-CLI command.', 'staatic');
    }

    public function defaultValue()
    {
        return 180;
    }
}
