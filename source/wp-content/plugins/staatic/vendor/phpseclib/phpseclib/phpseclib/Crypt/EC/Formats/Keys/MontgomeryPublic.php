<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use LengthException;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Curve25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Curve448;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class MontgomeryPublic
{
    const IS_INVISIBLE = \true;
    public static function load($key, $password = '')
    {
        switch (strlen($key)) {
            case 32:
                $curve = new Curve25519();
                break;
            case 56:
                $curve = new Curve448();
                break;
            default:
                throw new LengthException('The only supported lengths are 32 and 56');
        }
        $components = ['curve' => $curve];
        $components['QA'] = [$components['curve']->convertInteger(new BigInteger(strrev($key), 256))];
        return $components;
    }
    /**
     * @param MontgomeryCurve $curve
     * @param mixed[] $publicKey
     */
    public static function savePublicKey($curve, $publicKey)
    {
        return strrev($publicKey[0]->toBytes());
    }
}
