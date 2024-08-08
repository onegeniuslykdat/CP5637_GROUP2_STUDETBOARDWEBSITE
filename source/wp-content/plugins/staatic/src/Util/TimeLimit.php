<?php

declare(strict_types=1);

namespace Staatic\WordPress\Util;

final class TimeLimit
{
    public static function setTimeLimit(int $newLimit): bool
    {
        $currentLimit = (int) ini_get('max_execution_time');
        if ($currentLimit === 0) {
            return \true;
        }
        if (strpos(ini_get('disable_functions'), 'set_time_limit') !== false) {
            return \false;
        }
        $result = set_time_limit($newLimit);

        // We're not using the result of set_time_limit, since it may
        // return false when using xdebug even though it did succeed.
        return (int) ini_get('max_execution_time') === $newLimit;
    }
}
