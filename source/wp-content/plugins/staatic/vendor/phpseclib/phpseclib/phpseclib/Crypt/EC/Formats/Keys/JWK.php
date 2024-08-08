<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use Exception;
use RuntimeException;
use ReflectionClass;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\JWK as Progenitor;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\secp256k1;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\secp256r1;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\secp384r1;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\secp521r1;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class JWK extends Progenitor
{
    use Common;
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        switch ($key->kty) {
            case 'EC':
                switch ($key->crv) {
                    case 'P-256':
                    case 'P-384':
                    case 'P-521':
                    case 'secp256k1':
                        break;
                    default:
                        throw new UnsupportedCurveException('Only P-256, P-384, P-521 and secp256k1 curves are accepted (' . $key->crv . ' provided)');
                }
                break;
            case 'OKP':
                switch ($key->crv) {
                    case 'Ed25519':
                    case 'Ed448':
                        break;
                    default:
                        throw new UnsupportedCurveException('Only Ed25519 and Ed448 curves are accepted (' . $key->crv . ' provided)');
                }
                break;
            default:
                throw new Exception('Only EC and OKP JWK keys are supported');
        }
        $curve = 'Staatic\Vendor\phpseclib3\Crypt\EC\Curves\\' . str_replace('P-', 'nistp', $key->crv);
        $curve = new $curve();
        if ($curve instanceof TwistedEdwardsCurve) {
            $QA = self::extractPoint(Strings::base64url_decode($key->x), $curve);
            if (!isset($key->d)) {
                return compact('curve', 'QA');
            }
            $arr = $curve->extractSecret(Strings::base64url_decode($key->d));
            return compact('curve', 'QA') + $arr;
        }
        $QA = [$curve->convertInteger(new BigInteger(Strings::base64url_decode($key->x), 256)), $curve->convertInteger(new BigInteger(Strings::base64url_decode($key->y), 256))];
        if (!$curve->verifyPoint($QA)) {
            throw new RuntimeException('Unable to verify that point exists on curve');
        }
        if (!isset($key->d)) {
            return compact('curve', 'QA');
        }
        $dA = new BigInteger(Strings::base64url_decode($key->d), 256);
        $curve->rangeCheck($dA);
        return compact('curve', 'dA', 'QA');
    }
    /**
     * @param mixed $curve
     */
    private static function getAlias($curve)
    {
        switch (\true) {
            case $curve instanceof secp256r1:
                return 'P-256';
            case $curve instanceof secp384r1:
                return 'P-384';
            case $curve instanceof secp521r1:
                return 'P-521';
            case $curve instanceof secp256k1:
                return 'secp256k1';
        }
        $reflect = new ReflectionClass($curve);
        $curveName = $reflect->isFinal() ? $reflect->getParentClass()->getShortName() : $reflect->getShortName();
        throw new UnsupportedCurveException("{$curveName} is not a supported curve");
    }
    /**
     * @param mixed $curve
     */
    private static function savePublicKeyHelper($curve, array $publicKey)
    {
        if ($curve instanceof TwistedEdwardsCurve) {
            return ['kty' => 'OKP', 'crv' => ($curve instanceof Ed25519) ? 'Ed25519' : 'Ed448', 'x' => Strings::base64url_encode($curve->encodePoint($publicKey))];
        }
        return ['kty' => 'EC', 'crv' => self::getAlias($curve), 'x' => Strings::base64url_encode($publicKey[0]->toBytes()), 'y' => Strings::base64url_encode($publicKey[1]->toBytes())];
    }
    /**
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePublicKey($curve, $publicKey, $options = [])
    {
        $key = self::savePublicKeyHelper($curve, $publicKey);
        return self::wrapKey($key, $options);
    }
    /**
     * @param BigInteger $privateKey
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePrivateKey($privateKey, $curve, $publicKey, $secret = null, $password = '', $options = [])
    {
        $key = self::savePublicKeyHelper($curve, $publicKey);
        $key['d'] = ($curve instanceof TwistedEdwardsCurve) ? $secret : $privateKey->toBytes();
        $key['d'] = Strings::base64url_encode($key['d']);
        return self::wrapKey($key, $options);
    }
}
