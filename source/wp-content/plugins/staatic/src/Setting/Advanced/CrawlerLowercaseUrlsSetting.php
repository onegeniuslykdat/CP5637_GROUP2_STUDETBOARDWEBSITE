<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class CrawlerLowercaseUrlsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_crawler_lowercase_urls';
    }

    public function type(): string
    {
        return self::TYPE_BOOLEAN;
    }

    public function label(): string
    {
        return __('Lowercase URLs', 'staatic');
    }

    public function extendedLabel(): ?string
    {
        return __('Enforce lowercase URLs', 'staatic');
    }

    public function description(): ?string
    {
        return __('If enabled, automatically converts all URLs to lowercase for consistent formatting and to prevent case-sensitive duplicate content.', 'staatic');
    }

    public function defaultValue()
    {
        return \false;
    }
}
