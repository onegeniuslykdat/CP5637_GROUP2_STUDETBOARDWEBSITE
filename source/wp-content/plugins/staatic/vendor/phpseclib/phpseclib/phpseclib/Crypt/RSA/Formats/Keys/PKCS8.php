<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS8 extends Progenitor
{
    const OID_NAME = 'rsaEncryption';
    const OID_VALUE = '1.2.840.113549.1.1.1';
    protected static $childOIDsLoaded = \false;
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        if (isset($key['privateKey'])) {
            $components['isPublicKey'] = \false;
            $type = 'private';
        } else {
            $components['isPublicKey'] = \true;
            $type = 'public';
        }
        $result = $components + PKCS1::load($key[$type . 'Key']);
        if (isset($key['meta'])) {
            $result['meta'] = $key['meta'];
        }
        return $result;
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param BigInteger $d
     * @param mixed[] $primes
     * @param mixed[] $exponents
     * @param mixed[] $coefficients
     * @param mixed[] $options
     */
    public static function savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients, $password = '', $options = [])
    {
        $key = PKCS1::savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients);
        $key = ASN1::extractBER($key);
        return self::wrapPrivateKey($key, [], null, $password, null, '', $options);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param mixed[] $options
     */
    public static function savePublicKey($n, $e, $options = [])
    {
        $key = PKCS1::savePublicKey($n, $e);
        $key = ASN1::extractBER($key);
        return self::wrapPublicKey($key, null);
    }
}
