<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class OpenSSH extends Progenitor
{
    protected static $types = ['ssh-rsa'];
    public static function load($key, $password = '')
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }
        $parsed = parent::load($key, $password);
        if (isset($parsed['paddedKey'])) {
            list($type) = Strings::unpackSSH2('s', $parsed['paddedKey']);
            if ($type != $parsed['type']) {
                throw new RuntimeException("The public and private keys are not of the same type ({$type} vs {$parsed['type']})");
            }
            $primes = $coefficients = [];
            list($modulus, $publicExponent, $privateExponent, $coefficients[2], $primes[1], $primes[2], $comment, ) = Strings::unpackSSH2('i6s', $parsed['paddedKey']);
            $temp = $primes[1]->subtract($one);
            $exponents = [1 => $publicExponent->modInverse($temp)];
            $temp = $primes[2]->subtract($one);
            $exponents[] = $publicExponent->modInverse($temp);
            $isPublicKey = \false;
            return compact('publicExponent', 'modulus', 'privateExponent', 'primes', 'coefficients', 'exponents', 'comment', 'isPublicKey');
        }
        list($publicExponent, $modulus) = Strings::unpackSSH2('ii', $parsed['publicKey']);
        return ['isPublicKey' => \true, 'modulus' => $modulus, 'publicExponent' => $publicExponent, 'comment' => $parsed['comment']];
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param mixed[] $options
     */
    public static function savePublicKey($n, $e, $options = [])
    {
        $RSAPublicKey = Strings::packSSH2('sii', 'ssh-rsa', $e, $n);
        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $RSAPublicKey;
        }
        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $RSAPublicKey = 'ssh-rsa ' . base64_encode($RSAPublicKey) . ' ' . $comment;
        return $RSAPublicKey;
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
        $publicKey = self::savePublicKey($n, $e, ['binary' => \true]);
        $privateKey = Strings::packSSH2('si6', 'ssh-rsa', $n, $e, $d, $coefficients[2], $primes[1], $primes[2]);
        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
