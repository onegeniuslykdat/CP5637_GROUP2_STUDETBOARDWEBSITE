<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECParameters;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECPrivateKey;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed448;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS8 extends Progenitor
{
    use Common;
    const OID_NAME = ['id-ecPublicKey', 'id-Ed25519', 'id-Ed448'];
    const OID_VALUE = ['1.2.840.10045.2.1', '1.3.101.112', '1.3.101.113'];
    public static function load($key, $password = '')
    {
        self::initialize_static_variables();
        $key = parent::load($key, $password);
        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';
        switch ($key[$type . 'Algorithm']['algorithm']) {
            case 'id-Ed25519':
            case 'id-Ed448':
                return self::loadEdDSA($key);
        }
        $decoded = ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $params = ASN1::asn1map($decoded[0], ECParameters::MAP);
        if (!$params) {
            throw new RuntimeException('Unable to decode the parameters using Maps\ECParameters');
        }
        $components = [];
        $components['curve'] = self::loadCurveByParam($params);
        if ($type == 'publicKey') {
            $components['QA'] = self::extractPoint("\x00" . $key['publicKey'], $components['curve']);
            return $components;
        }
        $decoded = ASN1::decodeBER($key['privateKey']);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $key = ASN1::asn1map($decoded[0], ECPrivateKey::MAP);
        if (isset($key['parameters']) && $params != $key['parameters']) {
            throw new RuntimeException('The PKCS8 parameter field does not match the private key parameter field');
        }
        $components['dA'] = new BigInteger($key['privateKey'], 256);
        $components['curve']->rangeCheck($components['dA']);
        $components['QA'] = isset($key['publicKey']) ? self::extractPoint($key['publicKey'], $components['curve']) : $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);
        return $components;
    }
    private static function loadEdDSA(array $key)
    {
        $components = [];
        if (isset($key['privateKey'])) {
            $components['curve'] = ($key['privateKeyAlgorithm']['algorithm'] == 'id-Ed25519') ? new Ed25519() : new Ed448();
            $expected = chr(ASN1::TYPE_OCTET_STRING) . ASN1::encodeLength($components['curve']::SIZE);
            if (substr($key['privateKey'], 0, 2) != $expected) {
                throw new RuntimeException('The first two bytes of the ' . $key['privateKeyAlgorithm']['algorithm'] . ' private key field should be 0x' . bin2hex($expected));
            }
            $arr = $components['curve']->extractSecret(substr($key['privateKey'], 2));
            $components['dA'] = $arr['dA'];
            $components['secret'] = $arr['secret'];
        }
        if (isset($key['publicKey'])) {
            if (!isset($components['curve'])) {
                $components['curve'] = ($key['publicKeyAlgorithm']['algorithm'] == 'id-Ed25519') ? new Ed25519() : new Ed448();
            }
            $components['QA'] = self::extractPoint($key['publicKey'], $components['curve']);
        }
        if (isset($key['privateKey']) && !isset($components['QA'])) {
            $components['QA'] = $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);
        }
        return $components;
    }
    /**
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePublicKey($curve, $publicKey, $options = [])
    {
        self::initialize_static_variables();
        if ($curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('Montgomery Curves are not supported');
        }
        if ($curve instanceof TwistedEdwardsCurve) {
            return self::wrapPublicKey($curve->encodePoint($publicKey), null, ($curve instanceof Ed25519) ? 'id-Ed25519' : 'id-Ed448');
        }
        $params = new Element(self::encodeParameters($curve, \false, $options));
        $key = "\x04" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();
        return self::wrapPublicKey($key, $params, 'id-ecPublicKey');
    }
    /**
     * @param BigInteger $privateKey
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePrivateKey($privateKey, $curve, $publicKey, $secret = null, $password = '', $options = [])
    {
        self::initialize_static_variables();
        if ($curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('Montgomery Curves are not supported');
        }
        if ($curve instanceof TwistedEdwardsCurve) {
            return self::wrapPrivateKey(chr(ASN1::TYPE_OCTET_STRING) . ASN1::encodeLength($curve::SIZE) . $secret, [], null, $password, ($curve instanceof Ed25519) ? 'id-Ed25519' : 'id-Ed448');
        }
        $publicKey = "\x04" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();
        $params = new Element(self::encodeParameters($curve, \false, $options));
        $key = ['version' => 'ecPrivkeyVer1', 'privateKey' => $privateKey->toBytes(), 'publicKey' => "\x00" . $publicKey];
        $key = ASN1::encodeDER($key, ECPrivateKey::MAP);
        return self::wrapPrivateKey($key, [], $params, $password, 'id-ecPublicKey', '', $options);
    }
}
