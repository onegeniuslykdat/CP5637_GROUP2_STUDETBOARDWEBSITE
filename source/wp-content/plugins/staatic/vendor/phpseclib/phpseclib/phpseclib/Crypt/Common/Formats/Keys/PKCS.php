<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys;

abstract class PKCS
{
    const MODE_ANY = 0;
    const MODE_PEM = 1;
    const MODE_DER = 2;
    protected static $format = self::MODE_ANY;
    public static function requirePEM()
    {
        self::$format = self::MODE_PEM;
    }
    public static function requireDER()
    {
        self::$format = self::MODE_DER;
    }
    public static function requireAny()
    {
        self::$format = self::MODE_ANY;
    }
}
