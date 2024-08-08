<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DH\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DHParameter;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS8 extends Progenitor
{
    const OID_NAME = 'dhKeyAgreement';
    const OID_VALUE = '1.2.840.113549.1.3.1';
    protected static $childOIDsLoaded = \false;
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';
        $decoded = ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (empty($decoded)) {
            throw new RuntimeException('Unable to decode BER of parameters');
        }
        $components = ASN1::asn1map($decoded[0], DHParameter::MAP);
        if (!is_array($components)) {
            throw new RuntimeException('Unable to perform ASN1 mapping on parameters');
        }
        $decoded = ASN1::decodeBER($key[$type]);
        switch (\true) {
            case !isset($decoded):
            case !isset($decoded[0]['content']):
            case !$decoded[0]['content'] instanceof BigInteger:
                throw new RuntimeException('Unable to decode BER of parameters');
        }
        $components[$type] = $decoded[0]['content'];
        return $components;
    }
    /**
     * @param BigInteger $prime
     * @param BigInteger $base
     * @param BigInteger $privateKey
     * @param BigInteger $publicKey
     * @param mixed[] $options
     */
    public static function savePrivateKey($prime, $base, $privateKey, $publicKey, $password = '', $options = [])
    {
        $params = ['prime' => $prime, 'base' => $base];
        $params = ASN1::encodeDER($params, DHParameter::MAP);
        $params = new Element($params);
        $key = ASN1::encodeDER($privateKey, ['type' => ASN1::TYPE_INTEGER]);
        return self::wrapPrivateKey($key, [], $params, $password, null, '', $options);
    }
    /**
     * @param BigInteger $prime
     * @param BigInteger $base
     * @param BigInteger $publicKey
     * @param mixed[] $options
     */
    public static function savePublicKey($prime, $base, $publicKey, $options = [])
    {
        $params = ['prime' => $prime, 'base' => $base];
        $params = ASN1::encodeDER($params, DHParameter::MAP);
        $params = new Element($params);
        $key = ASN1::encodeDER($publicKey, ['type' => ASN1::TYPE_INTEGER]);
        return self::wrapPublicKey($key, $params);
    }
}
