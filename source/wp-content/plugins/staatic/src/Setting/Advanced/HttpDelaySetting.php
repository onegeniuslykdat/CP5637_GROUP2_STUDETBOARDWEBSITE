<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class HttpDelaySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_http_delay';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('HTTP Delay', 'staatic');
    }

    public function description(): ?string
    {
        return __('The number of milliseconds for HTTP requests to be delayed.', 'staatic');
    }

    public function defaultValue()
    {
        return 0;
    }
}
