<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys;

use RuntimeException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class OpenSSH extends Progenitor
{
    protected static $types = ['ssh-dss'];
    public static function load($key, $password = '')
    {
        $parsed = parent::load($key, $password);
        if (isset($parsed['paddedKey'])) {
            list($type) = Strings::unpackSSH2('s', $parsed['paddedKey']);
            if ($type != $parsed['type']) {
                throw new RuntimeException("The public and private keys are not of the same type ({$type} vs {$parsed['type']})");
            }
            list($p, $q, $g, $y, $x, $comment) = Strings::unpackSSH2('i5s', $parsed['paddedKey']);
            return compact('p', 'q', 'g', 'y', 'x', 'comment');
        }
        list($p, $q, $g, $y) = Strings::unpackSSH2('iiii', $parsed['publicKey']);
        $comment = $parsed['comment'];
        return compact('p', 'q', 'g', 'y', 'comment');
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
        if ($q->getLength() != 160) {
            throw new InvalidArgumentException('SSH only supports keys with an N (length of Group Order q) of 160');
        }
        $DSAPublicKey = Strings::packSSH2('siiii', 'ssh-dss', $p, $q, $g, $y);
        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $DSAPublicKey;
        }
        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $DSAPublicKey = 'ssh-dss ' . base64_encode($DSAPublicKey) . ' ' . $comment;
        return $DSAPublicKey;
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
        $publicKey = self::savePublicKey($p, $q, $g, $y, ['binary' => \true]);
        $privateKey = Strings::packSSH2('si5', 'ssh-dss', $p, $q, $g, $y, $x);
        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
