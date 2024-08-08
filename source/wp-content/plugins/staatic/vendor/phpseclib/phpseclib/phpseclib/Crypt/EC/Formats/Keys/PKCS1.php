<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use UnexpectedValueException;
use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECPrivateKey;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECParameters;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS1 extends Progenitor
{
    use Common;
    public static function load($key, $password = '')
    {
        self::initialize_static_variables();
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        if (strpos($key, 'BEGIN EC PARAMETERS') && strpos($key, 'BEGIN EC PRIVATE KEY')) {
            $components = [];
            preg_match('#-*BEGIN EC PRIVATE KEY-*[^-]*-*END EC PRIVATE KEY-*#s', $key, $matches);
            $decoded = parent::load($matches[0], $password);
            $decoded = ASN1::decodeBER($decoded);
            if (!$decoded) {
                throw new RuntimeException('Unable to decode BER');
            }
            $ecPrivate = ASN1::asn1map($decoded[0], ECPrivateKey::MAP);
            if (!is_array($ecPrivate)) {
                throw new RuntimeException('Unable to perform ASN1 mapping');
            }
            if (isset($ecPrivate['parameters'])) {
                $components['curve'] = self::loadCurveByParam($ecPrivate['parameters']);
            }
            preg_match('#-*BEGIN EC PARAMETERS-*[^-]*-*END EC PARAMETERS-*#s', $key, $matches);
            $decoded = parent::load($matches[0], '');
            $decoded = ASN1::decodeBER($decoded);
            if (!$decoded) {
                throw new RuntimeException('Unable to decode BER');
            }
            $ecParams = ASN1::asn1map($decoded[0], ECParameters::MAP);
            if (!is_array($ecParams)) {
                throw new RuntimeException('Unable to perform ASN1 mapping');
            }
            $ecParams = self::loadCurveByParam($ecParams);
            if (isset($components['curve']) && self::encodeParameters($ecParams, \false, []) != self::encodeParameters($components['curve'], \false, [])) {
                throw new RuntimeException('EC PARAMETERS does not correspond to EC PRIVATE KEY');
            }
            if (!isset($components['curve'])) {
                $components['curve'] = $ecParams;
            }
            $components['dA'] = new BigInteger($ecPrivate['privateKey'], 256);
            $components['curve']->rangeCheck($components['dA']);
            $components['QA'] = isset($ecPrivate['publicKey']) ? self::extractPoint($ecPrivate['publicKey'], $components['curve']) : $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);
            return $components;
        }
        $key = parent::load($key, $password);
        $decoded = ASN1::decodeBER($key);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $key = ASN1::asn1map($decoded[0], ECParameters::MAP);
        if (is_array($key)) {
            return ['curve' => self::loadCurveByParam($key)];
        }
        $key = ASN1::asn1map($decoded[0], ECPrivateKey::MAP);
        if (!is_array($key)) {
            throw new RuntimeException('Unable to perform ASN1 mapping');
        }
        if (!isset($key['parameters'])) {
            throw new RuntimeException('Key cannot be loaded without parameters');
        }
        $components = [];
        $components['curve'] = self::loadCurveByParam($key['parameters']);
        $components['dA'] = new BigInteger($key['privateKey'], 256);
        $components['QA'] = isset($ecPrivate['publicKey']) ? self::extractPoint($ecPrivate['publicKey'], $components['curve']) : $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);
        return $components;
    }
    /**
     * @param BaseCurve $curve
     * @param mixed[] $options
     */
    public static function saveParameters($curve, $options = [])
    {
        self::initialize_static_variables();
        if ($curve instanceof TwistedEdwardsCurve || $curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('TwistedEdwards and Montgomery Curves are not supported');
        }
        $key = self::encodeParameters($curve, \false, $options);
        return "-----BEGIN EC PARAMETERS-----\r\n" . chunk_split(Strings::base64_encode($key), 64) . "-----END EC PARAMETERS-----\r\n";
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
        if ($curve instanceof TwistedEdwardsCurve || $curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('TwistedEdwards Curves are not supported');
        }
        $publicKey = "\x04" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();
        $key = ['version' => 'ecPrivkeyVer1', 'privateKey' => $privateKey->toBytes(), 'parameters' => new Element(self::encodeParameters($curve)), 'publicKey' => "\x00" . $publicKey];
        $key = ASN1::encodeDER($key, ECPrivateKey::MAP);
        return self::wrapPrivateKey($key, 'EC', $password, $options);
    }
}
