<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class HttpConcurrencySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_http_concurrency';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('HTTP Concurrency', 'staatic');
    }

    public function description(): ?string
    {
        return __('The number of simultaneous HTTP connections allowed.', 'staatic');
    }

    public function defaultValue()
    {
        return 4;
    }
}
