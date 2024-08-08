<?php

declare(strict_types=1);

namespace Staatic\WordPress\Util;

final class HttpUtil
{
    public const USER_AGENT_NAME = 'StaaticWordPress';

    public static function userAgent(): string
    {
        global $wp_version;

        return sprintf('%s/%s (WordPress %s; %s)', self::USER_AGENT_NAME, \STAATIC_VERSION, $wp_version, home_url('/'));
    }
}
