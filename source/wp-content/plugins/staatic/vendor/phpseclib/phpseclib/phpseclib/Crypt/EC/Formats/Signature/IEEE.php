<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Signature;

use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class IEEE
{
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return \false;
        }
        $len = strlen($sig);
        if ($len & 1) {
            return \false;
        }
        $r = new BigInteger(substr($sig, 0, $len >> 1), 256);
        $s = new BigInteger(substr($sig, $len >> 1), 256);
        return compact('r', 's');
    }
    /**
     * @param BigInteger $r
     * @param BigInteger $s
     */
    public static function save($r, $s)
    {
        $r = $r->toBytes();
        $s = $s->toBytes();
        $len = max(strlen($r), strlen($s));
        return str_pad($r, $len, "\x00", \STR_PAD_LEFT) . str_pad($s, $len, "\x00", \STR_PAD_LEFT);
    }
}
