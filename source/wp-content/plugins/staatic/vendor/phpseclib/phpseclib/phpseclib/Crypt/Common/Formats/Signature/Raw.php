<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Signature;

use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class Raw
{
    public static function load($sig)
    {
        switch (\true) {
            case !is_array($sig):
            case !isset($sig['r']) || !isset($sig['s']):
            case !$sig['r'] instanceof BigInteger:
            case !$sig['s'] instanceof BigInteger:
                return \false;
        }
        return ['r' => $sig['r'], 's' => $sig['s']];
    }
    /**
     * @param BigInteger $r
     * @param BigInteger $s
     */
    public static function save($r, $s)
    {
        return compact('r', 's');
    }
}
