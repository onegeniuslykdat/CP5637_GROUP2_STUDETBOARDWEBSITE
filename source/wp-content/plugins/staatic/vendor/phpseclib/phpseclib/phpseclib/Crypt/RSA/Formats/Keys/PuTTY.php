<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PuTTY as Progenitor;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PuTTY extends Progenitor
{
    const PUBLIC_HANDLER = 'Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys\OpenSSH';
    protected static $types = ['ssh-rsa'];
    public static function load($key, $password = '')
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }
        $components = parent::load($key, $password);
        if (!isset($components['private'])) {
            return $components;
        }
        extract($components);
        unset($components['public'], $components['private']);
        $isPublicKey = \false;
        $result = Strings::unpackSSH2('ii', $public);
        if ($result === \false) {
            throw new UnexpectedValueException('Key appears to be malformed');
        }
        list($publicExponent, $modulus) = $result;
        $result = Strings::unpackSSH2('iiii', $private);
        if ($result === \false) {
            throw new UnexpectedValueException('Key appears to be malformed');
        }
        $primes = $coefficients = [];
        list($privateExponent, $primes[1], $primes[2], $coefficients[2]) = $result;
        $temp = $primes[1]->subtract($one);
        $exponents = [1 => $publicExponent->modInverse($temp)];
        $temp = $primes[2]->subtract($one);
        $exponents[] = $publicExponent->modInverse($temp);
        return compact('publicExponent', 'modulus', 'privateExponent', 'primes', 'coefficients', 'exponents', 'comment', 'isPublicKey');
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
        if (count($primes) != 2) {
            throw new InvalidArgumentException('PuTTY does not support multi-prime RSA keys');
        }
        $public = Strings::packSSH2('ii', $e, $n);
        $private = Strings::packSSH2('iiii', $d, $primes[1], $primes[2], $coefficients[2]);
        return self::wrapPrivateKey($public, $private, 'ssh-rsa', $password, $options);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     */
    public static function savePublicKey($n, $e)
    {
        return self::wrapPublicKey(Strings::packSSH2('ii', $e, $n), 'ssh-rsa');
    }
}
