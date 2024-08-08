<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class OverrideSiteUrlSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_override_site_url';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Custom Origin URL', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: The default origin URL. */
            __('Optionally specify a custom URL for your dynamic WordPress site.<br>If left blank, <code>%s</code> will be used as the default URL.', 'staatic'),
            esc_html(home_url())
        );
    }

    public function defaultValue()
    {
        return null;
    }
}
