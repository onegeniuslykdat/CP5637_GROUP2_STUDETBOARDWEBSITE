<?php

declare(strict_types=1);

namespace Staatic\WordPress\DependencyInjection;

use wpdb;

final class WpdbWrapper
{
    public static function get(): wpdb
    {
        global $wpdb;

        return $wpdb;
    }
}
