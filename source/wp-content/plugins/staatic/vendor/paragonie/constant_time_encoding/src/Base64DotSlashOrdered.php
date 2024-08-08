<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

abstract class Base64DotSlashOrdered extends Base64
{
    /**
     * @param int $src
     */
    protected static function decode6Bits($src): int
    {
        $ret = -1;
        $ret += (0x2d - $src & $src - 0x3a) >> 8 & $src - 45;
        $ret += (0x40 - $src & $src - 0x5b) >> 8 & $src - 52;
        $ret += (0x60 - $src & $src - 0x7b) >> 8 & $src - 58;
        return $ret;
    }
    /**
     * @param int $src
     */
    protected static function encode6Bits($src): string
    {
        $src += 0x2e;
        $src += 0x39 - $src >> 8 & 7;
        $src += 0x5a - $src >> 8 & 6;
        return \pack('C', $src);
    }
}
