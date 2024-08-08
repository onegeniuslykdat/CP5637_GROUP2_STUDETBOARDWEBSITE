<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class HttpAuthenticationUsernameSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_http_auth_username';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Username', 'staatic');
    }
}
