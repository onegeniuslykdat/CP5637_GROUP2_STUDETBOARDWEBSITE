<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

use TypeError;
abstract class Binary
{
    /**
     * @param string $str
     */
    public static function safeStrlen($str): int
    {
        if (\function_exists('mb_strlen')) {
            return (int) \mb_strlen($str, '8bit');
        } else {
            return \strlen($str);
        }
    }
    /**
     * @param string $str
     * @param int $start
     * @param int|null $length
     */
    public static function safeSubstr($str, $start = 0, $length = null): string
    {
        if ($length === 0) {
            return '';
        }
        if (\function_exists('mb_substr')) {
            return \mb_substr($str, $start, $length, '8bit');
        }
        if ($length !== null) {
            return \substr($str, $start, $length);
        } else {
            return \substr($str, $start);
        }
    }
}
