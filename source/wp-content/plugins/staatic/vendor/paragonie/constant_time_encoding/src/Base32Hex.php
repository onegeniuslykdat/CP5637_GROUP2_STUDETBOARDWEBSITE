<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

abstract class Base32Hex extends Base32
{
    /**
     * @param int $src
     */
    protected static function decode5Bits($src): int
    {
        $ret = -1;
        $ret += (0x2f - $src & $src - 0x3a) >> 8 & $src - 47;
        $ret += (0x60 - $src & $src - 0x77) >> 8 & $src - 86;
        return $ret;
    }
    /**
     * @param int $src
     */
    protected static function decode5BitsUpper($src): int
    {
        $ret = -1;
        $ret += (0x2f - $src & $src - 0x3a) >> 8 & $src - 47;
        $ret += (0x40 - $src & $src - 0x57) >> 8 & $src - 54;
        return $ret;
    }
    /**
     * @param int $src
     */
    protected static function encode5Bits($src): string
    {
        $src += 0x30;
        $src += 0x39 - $src >> 8 & 39;
        return \pack('C', $src);
    }
    /**
     * @param int $src
     */
    protected static function encode5BitsUpper($src): string
    {
        $src += 0x30;
        $src += 0x39 - $src >> 8 & 7;
        return \pack('C', $src);
    }
}
