<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\RSAPrivateKey;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\RSAPublicKey;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS1 extends Progenitor
{
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        if (strpos($key, 'PUBLIC') !== \false) {
            $components = ['isPublicKey' => \true];
        } elseif (strpos($key, 'PRIVATE') !== \false) {
            $components = ['isPublicKey' => \false];
        } else {
            $components = [];
        }
        $key = parent::load($key, $password);
        $decoded = ASN1::decodeBER($key);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $key = ASN1::asn1map($decoded[0], RSAPrivateKey::MAP);
        if (is_array($key)) {
            $components += ['modulus' => $key['modulus'], 'publicExponent' => $key['publicExponent'], 'privateExponent' => $key['privateExponent'], 'primes' => [1 => $key['prime1'], $key['prime2']], 'exponents' => [1 => $key['exponent1'], $key['exponent2']], 'coefficients' => [2 => $key['coefficient']]];
            if ($key['version'] == 'multi') {
                foreach ($key['otherPrimeInfos'] as $primeInfo) {
                    $components['primes'][] = $primeInfo['prime'];
                    $components['exponents'][] = $primeInfo['exponent'];
                    $components['coefficients'][] = $primeInfo['coefficient'];
                }
            }
            if (!isset($components['isPublicKey'])) {
                $components['isPublicKey'] = \false;
            }
            return $components;
        }
        $key = ASN1::asn1map($decoded[0], RSAPublicKey::MAP);
        if (!is_array($key)) {
            throw new RuntimeException('Unable to perform ASN1 mapping');
        }
        if (!isset($components['isPublicKey'])) {
            $components['isPublicKey'] = \true;
        }
        return $components + $key;
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
        $num_primes = count($primes);
        $key = ['version' => ($num_primes == 2) ? 'two-prime' : 'multi', 'modulus' => $n, 'publicExponent' => $e, 'privateExponent' => $d, 'prime1' => $primes[1], 'prime2' => $primes[2], 'exponent1' => $exponents[1], 'exponent2' => $exponents[2], 'coefficient' => $coefficients[2]];
        for ($i = 3; $i <= $num_primes; $i++) {
            $key['otherPrimeInfos'][] = ['prime' => $primes[$i], 'exponent' => $exponents[$i], 'coefficient' => $coefficients[$i]];
        }
        $key = ASN1::encodeDER($key, RSAPrivateKey::MAP);
        return self::wrapPrivateKey($key, 'RSA', $password, $options);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     */
    public static function savePublicKey($n, $e)
    {
        $key = ['modulus' => $n, 'publicExponent' => $e];
        $key = ASN1::encodeDER($key, RSAPublicKey::MAP);
        return self::wrapPublicKey($key, 'RSA');
    }
}
