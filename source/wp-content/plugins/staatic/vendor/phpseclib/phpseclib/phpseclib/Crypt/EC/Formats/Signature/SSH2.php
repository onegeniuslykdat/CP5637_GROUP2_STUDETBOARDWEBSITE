<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Signature;

use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class SSH2
{
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return \false;
        }
        $result = Strings::unpackSSH2('ss', $sig);
        if ($result === \false) {
            return \false;
        }
        list($type, $blob) = $result;
        switch ($type) {
            case 'ecdsa-sha2-nistp256':
            case 'ecdsa-sha2-nistp384':
            case 'ecdsa-sha2-nistp521':
                break;
            default:
                return \false;
        }
        $result = Strings::unpackSSH2('ii', $blob);
        if ($result === \false) {
            return \false;
        }
        return ['r' => $result[0], 's' => $result[1]];
    }
    /**
     * @param BigInteger $r
     * @param BigInteger $s
     */
    public static function save($r, $s, $curve)
    {
        switch ($curve) {
            case 'secp256r1':
                $curve = 'nistp256';
                break;
            case 'secp384r1':
                $curve = 'nistp384';
                break;
            case 'secp521r1':
                $curve = 'nistp521';
                break;
            default:
                return \false;
        }
        $blob = Strings::packSSH2('ii', $r, $s);
        return Strings::packSSH2('ss', 'ecdsa-sha2-' . $curve, $blob);
    }
}
