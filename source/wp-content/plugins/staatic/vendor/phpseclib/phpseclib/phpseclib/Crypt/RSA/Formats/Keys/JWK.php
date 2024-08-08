<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use RuntimeException;
use UnexpectedValueException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\JWK as Progenitor;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class JWK extends Progenitor
{
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        if ($key->kty != 'RSA') {
            throw new RuntimeException('Only RSA JWK keys are supported');
        }
        $count = $publicCount = 0;
        $vars = ['n', 'e', 'd', 'p', 'q', 'dp', 'dq', 'qi'];
        foreach ($vars as $var) {
            if (!isset($key->{$var}) || !is_string($key->{$var})) {
                continue;
            }
            $count++;
            $value = new BigInteger(Strings::base64url_decode($key->{$var}), 256);
            switch ($var) {
                case 'n':
                    $publicCount++;
                    $components['modulus'] = $value;
                    break;
                case 'e':
                    $publicCount++;
                    $components['publicExponent'] = $value;
                    break;
                case 'd':
                    $components['privateExponent'] = $value;
                    break;
                case 'p':
                    $components['primes'][1] = $value;
                    break;
                case 'q':
                    $components['primes'][2] = $value;
                    break;
                case 'dp':
                    $components['exponents'][1] = $value;
                    break;
                case 'dq':
                    $components['exponents'][2] = $value;
                    break;
                case 'qi':
                    $components['coefficients'][2] = $value;
            }
        }
        if ($count == count($vars)) {
            return $components + ['isPublicKey' => \false];
        }
        if ($count == 2 && $publicCount == 2) {
            return $components + ['isPublicKey' => \true];
        }
        throw new UnexpectedValueException('Key does not have an appropriate number of RSA parameters');
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
            throw new InvalidArgumentException('JWK does not support multi-prime RSA keys');
        }
        $key = ['kty' => 'RSA', 'n' => Strings::base64url_encode($n->toBytes()), 'e' => Strings::base64url_encode($e->toBytes()), 'd' => Strings::base64url_encode($d->toBytes()), 'p' => Strings::base64url_encode($primes[1]->toBytes()), 'q' => Strings::base64url_encode($primes[2]->toBytes()), 'dp' => Strings::base64url_encode($exponents[1]->toBytes()), 'dq' => Strings::base64url_encode($exponents[2]->toBytes()), 'qi' => Strings::base64url_encode($coefficients[2]->toBytes())];
        return self::wrapKey($key, $options);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param mixed[] $options
     */
    public static function savePublicKey($n, $e, $options = [])
    {
        $key = ['kty' => 'RSA', 'n' => Strings::base64url_encode($n->toBytes()), 'e' => Strings::base64url_encode($e->toBytes())];
        return self::wrapKey($key, $options);
    }
}
