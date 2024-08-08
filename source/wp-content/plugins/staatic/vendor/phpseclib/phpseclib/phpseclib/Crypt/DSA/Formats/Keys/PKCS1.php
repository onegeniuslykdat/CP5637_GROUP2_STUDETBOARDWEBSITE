<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DSAParams;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DSAPrivateKey;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DSAPublicKey;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS1 extends Progenitor
{
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        $decoded = ASN1::decodeBER($key);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $key = ASN1::asn1map($decoded[0], DSAParams::MAP);
        if (is_array($key)) {
            return $key;
        }
        $key = ASN1::asn1map($decoded[0], DSAPrivateKey::MAP);
        if (is_array($key)) {
            return $key;
        }
        $key = ASN1::asn1map($decoded[0], DSAPublicKey::MAP);
        if (is_array($key)) {
            return $key;
        }
        throw new RuntimeException('Unable to perform ASN1 mapping');
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     */
    public static function saveParameters($p, $q, $g)
    {
        $key = ['p' => $p, 'q' => $q, 'g' => $g];
        $key = ASN1::encodeDER($key, DSAParams::MAP);
        return "-----BEGIN DSA PARAMETERS-----\r\n" . chunk_split(Strings::base64_encode($key), 64) . "-----END DSA PARAMETERS-----\r\n";
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     * @param BigInteger $x
     * @param mixed[] $options
     */
    public static function savePrivateKey($p, $q, $g, $y, $x, $password = '', $options = [])
    {
        $key = ['version' => 0, 'p' => $p, 'q' => $q, 'g' => $g, 'y' => $y, 'x' => $x];
        $key = ASN1::encodeDER($key, DSAPrivateKey::MAP);
        return self::wrapPrivateKey($key, 'DSA', $password, $options);
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     */
    public static function savePublicKey($p, $q, $g, $y)
    {
        $key = ASN1::encodeDER($y, DSAPublicKey::MAP);
        return self::wrapPublicKey($key, 'DSA');
    }
}
