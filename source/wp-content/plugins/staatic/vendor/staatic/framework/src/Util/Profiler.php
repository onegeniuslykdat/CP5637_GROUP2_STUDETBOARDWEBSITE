<?php

namespace Staatic\Framework\Util;

final class Profiler
{
    /**
     * @var mixed[]
     */
    public static $startTimes = [];
    public static function start(): string
    {
        $id = random_bytes(24);
        self::$startTimes[$id] = self::timeInMs();
        return $id;
    }
    public static function stop(string $id): string
    {
        $timeTaken = self::timeInMs() - self::$startTimes[$id];
        unset(self::$startTimes[$id]);
        return number_format($timeTaken / 1000.0, 3) . 's';
    }
    private static function timeInMs(): int
    {
        if (function_exists('hrtime')) {
            return hrtime(\true) / 1000000.0;
        }
        return floor(microtime(\true) * 1000.0);
    }
}
