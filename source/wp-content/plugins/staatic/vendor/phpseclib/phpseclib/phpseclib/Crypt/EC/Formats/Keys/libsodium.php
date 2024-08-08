<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class libsodium
{
    use Common;
    const IS_INVISIBLE = \true;
    public static function load($key, $password = '')
    {
        switch (strlen($key)) {
            case 32:
                $public = $key;
                break;
            case 64:
                $private = substr($key, 0, 32);
                $public = substr($key, -32);
                break;
            case 96:
                $public = substr($key, -32);
                if (substr($key, 32, 32) != $public) {
                    throw new RuntimeException('Keys with 96 bytes should have the 2nd and 3rd set of 32 bytes match');
                }
                $private = substr($key, 0, 32);
                break;
            default:
                throw new RuntimeException('libsodium keys need to either be 32 bytes long, 64 bytes long or 96 bytes long');
        }
        $curve = new Ed25519();
        $components = ['curve' => $curve];
        if (isset($private)) {
            $arr = $curve->extractSecret($private);
            $components['dA'] = $arr['dA'];
            $components['secret'] = $arr['secret'];
        }
        $components['QA'] = isset($public) ? self::extractPoint($public, $curve) : $curve->multiplyPoint($curve->getBasePoint(), $components['dA']);
        return $components;
    }
    /**
     * @param Ed25519 $curve
     * @param mixed[] $publicKey
     */
    public static function savePublicKey($curve, $publicKey)
    {
        return $curve->encodePoint($publicKey);
    }
    /**
     * @param BigInteger $privateKey
     * @param Ed25519 $curve
     * @param mixed[] $publicKey
     */
    public static function savePrivateKey($privateKey, $curve, $publicKey, $secret = null, $password = '')
    {
        if (!isset($secret)) {
            throw new RuntimeException('Private Key does not have a secret set');
        }
        if (strlen($secret) != 32) {
            throw new RuntimeException('Private Key secret is not of the correct length');
        }
        if (!empty($password) && is_string($password)) {
            throw new UnsupportedFormatException('libsodium private keys do not support encryption');
        }
        return $secret . $curve->encodePoint($publicKey);
    }
}
