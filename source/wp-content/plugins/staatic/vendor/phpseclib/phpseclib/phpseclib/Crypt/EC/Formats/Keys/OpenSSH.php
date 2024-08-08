<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use RuntimeException;
use ReflectionClass;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class OpenSSH extends Progenitor
{
    use Common;
    protected static $types = ['ecdsa-sha2-nistp256', 'ecdsa-sha2-nistp384', 'ecdsa-sha2-nistp521', 'ssh-ed25519'];
    public static function load($key, $password = '')
    {
        $parsed = parent::load($key, $password);
        if (isset($parsed['paddedKey'])) {
            $paddedKey = $parsed['paddedKey'];
            list($type) = Strings::unpackSSH2('s', $paddedKey);
            if ($type != $parsed['type']) {
                throw new RuntimeException("The public and private keys are not of the same type ({$type} vs {$parsed['type']})");
            }
            if ($type == 'ssh-ed25519') {
                list(, $key, $comment) = Strings::unpackSSH2('sss', $paddedKey);
                $key = libsodium::load($key);
                $key['comment'] = $comment;
                return $key;
            }
            list($curveName, $publicKey, $privateKey, $comment) = Strings::unpackSSH2('ssis', $paddedKey);
            $curve = self::loadCurveByParam(['namedCurve' => $curveName]);
            $curve->rangeCheck($privateKey);
            return ['curve' => $curve, 'dA' => $privateKey, 'QA' => self::extractPoint("\x00{$publicKey}", $curve), 'comment' => $comment];
        }
        if ($parsed['type'] == 'ssh-ed25519') {
            if (Strings::shift($parsed['publicKey'], 4) != "\x00\x00\x00 ") {
                throw new RuntimeException('Length of ssh-ed25519 key should be 32');
            }
            $curve = new Ed25519();
            $qa = self::extractPoint($parsed['publicKey'], $curve);
        } else {
            list($curveName, $publicKey) = Strings::unpackSSH2('ss', $parsed['publicKey']);
            $curveName = 'Staatic\Vendor\phpseclib3\Crypt\EC\Curves\\' . $curveName;
            $curve = new $curveName();
            $qa = self::extractPoint("\x00" . $publicKey, $curve);
        }
        return ['curve' => $curve, 'QA' => $qa, 'comment' => $parsed['comment']];
    }
    /**
     * @param mixed $curve
     */
    private static function getAlias($curve)
    {
        self::initialize_static_variables();
        $reflect = new ReflectionClass($curve);
        $name = $reflect->getShortName();
        $oid = self::$curveOIDs[$name];
        $aliases = array_filter(self::$curveOIDs, function ($v) use ($oid) {
            return $v == $oid;
        });
        $aliases = array_keys($aliases);
        for ($i = 0; $i < count($aliases); $i++) {
            if (in_array('ecdsa-sha2-' . $aliases[$i], self::$types)) {
                $alias = $aliases[$i];
                break;
            }
        }
        if (!isset($alias)) {
            throw new UnsupportedCurveException($name . ' is not a curve that the OpenSSH plugin supports');
        }
        return $alias;
    }
    /**
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePublicKey($curve, $publicKey, $options = [])
    {
        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        if ($curve instanceof Ed25519) {
            $key = Strings::packSSH2('ss', 'ssh-ed25519', $curve->encodePoint($publicKey));
            if (isset($options['binary']) ? $options['binary'] : self::$binary) {
                return $key;
            }
            $key = 'ssh-ed25519 ' . base64_encode($key) . ' ' . $comment;
            return $key;
        }
        $alias = self::getAlias($curve);
        $points = "\x04" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();
        $key = Strings::packSSH2('sss', 'ecdsa-sha2-' . $alias, $alias, $points);
        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $key;
        }
        $key = 'ecdsa-sha2-' . $alias . ' ' . base64_encode($key) . ' ' . $comment;
        return $key;
    }
    /**
     * @param BigInteger $privateKey
     * @param BaseCurve $curve
     * @param mixed[] $publicKey
     * @param mixed[] $options
     */
    public static function savePrivateKey($privateKey, $curve, $publicKey, $secret = null, $password = '', $options = [])
    {
        if ($curve instanceof Ed25519) {
            if (!isset($secret)) {
                throw new RuntimeException('Private Key does not have a secret set');
            }
            if (strlen($secret) != 32) {
                throw new RuntimeException('Private Key secret is not of the correct length');
            }
            $pubKey = $curve->encodePoint($publicKey);
            $publicKey = Strings::packSSH2('ss', 'ssh-ed25519', $pubKey);
            $privateKey = Strings::packSSH2('sss', 'ssh-ed25519', $pubKey, $secret . $pubKey);
            return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
        }
        $alias = self::getAlias($curve);
        $points = "\x04" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();
        $publicKey = self::savePublicKey($curve, $publicKey, ['binary' => \true]);
        $privateKey = Strings::packSSH2('sssi', 'ecdsa-sha2-' . $alias, $alias, $points, $privateKey);
        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
