<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DSAParams;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DSAPublicKey;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS8 extends Progenitor
{
    const OID_NAME = 'id-dsa';
    const OID_VALUE = '1.2.840.10040.4.1';
    protected static $childOIDsLoaded = \false;
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';
        $decoded = ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER of parameters');
        }
        $components = ASN1::asn1map($decoded[0], DSAParams::MAP);
        if (!is_array($components)) {
            throw new RuntimeException('Unable to perform ASN1 mapping on parameters');
        }
        $decoded = ASN1::decodeBER($key[$type]);
        if (empty($decoded)) {
            throw new RuntimeException('Unable to decode BER');
        }
        $var = ($type == 'privateKey') ? 'x' : 'y';
        $components[$var] = ASN1::asn1map($decoded[0], DSAPublicKey::MAP);
        if (!$components[$var] instanceof BigInteger) {
            throw new RuntimeException('Unable to perform ASN1 mapping');
        }
        if (isset($key['meta'])) {
            $components['meta'] = $key['meta'];
        }
        return $components;
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
        $params = ['p' => $p, 'q' => $q, 'g' => $g];
        $params = ASN1::encodeDER($params, DSAParams::MAP);
        $params = new Element($params);
        $key = ASN1::encodeDER($x, DSAPublicKey::MAP);
        return self::wrapPrivateKey($key, [], $params, $password, null, '', $options);
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     * @param mixed[] $options
     */
    public static function savePublicKey($p, $q, $g, $y, $options = [])
    {
        $params = ['p' => $p, 'q' => $q, 'g' => $g];
        $params = ASN1::encodeDER($params, DSAParams::MAP);
        $params = new Element($params);
        $key = ASN1::encodeDER($y, DSAPublicKey::MAP);
        return self::wrapPublicKey($key, $params);
    }
}
