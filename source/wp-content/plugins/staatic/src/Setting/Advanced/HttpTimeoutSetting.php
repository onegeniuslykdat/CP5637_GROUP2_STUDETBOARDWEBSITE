<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class HttpTimeoutSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_http_timeout';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('HTTP Timeout', 'staatic');
    }

    public function description(): ?string
    {
        return __('The number of seconds for HTTP connections and requests to timeout.', 'staatic');
    }

    public function defaultValue()
    {
        return 60;
    }
}
