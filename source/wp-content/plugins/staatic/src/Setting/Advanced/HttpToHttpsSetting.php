<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class HttpToHttpsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_http_https_to_http';
    }

    public function type(): string
    {
        return self::TYPE_BOOLEAN;
    }

    public function label(): string
    {
        return __('Downgrade HTTPS to HTTP', 'staatic');
    }

    public function extendedLabel(): ?string
    {
        return __('Downgrade HTTPS to HTTP while crawling site', 'staatic');
    }

    public function description(): ?string
    {
        return __('This option can be enabled in cases where WordPress is behind a HTTPS terminating load balancer preventing crawling to succeed otherwise.', 'staatic');
    }
}
