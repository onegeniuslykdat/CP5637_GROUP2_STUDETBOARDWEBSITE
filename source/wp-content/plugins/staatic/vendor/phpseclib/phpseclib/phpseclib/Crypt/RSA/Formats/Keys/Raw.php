<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class Raw
{
    public static function load($key, $password = '')
    {
        if (!is_array($key)) {
            throw new UnexpectedValueException('Key should be a array - not a ' . gettype($key));
        }
        $key = array_change_key_case($key, \CASE_LOWER);
        $components = ['isPublicKey' => \false];
        foreach (['e', 'exponent', 'publicexponent', 0, 'privateexponent', 'd'] as $index) {
            if (isset($key[$index])) {
                $components['publicExponent'] = $key[$index];
                break;
            }
        }
        foreach (['n', 'modulo', 'modulus', 1] as $index) {
            if (isset($key[$index])) {
                $components['modulus'] = $key[$index];
                break;
            }
        }
        if (!isset($components['publicExponent']) || !isset($components['modulus'])) {
            throw new UnexpectedValueException('Modulus / exponent not present');
        }
        if (isset($key['primes'])) {
            $components['primes'] = $key['primes'];
        } elseif (isset($key['p']) && isset($key['q'])) {
            $indices = [['p', 'q'], ['prime1', 'prime2']];
            foreach ($indices as $index) {
                list($i0, $i1) = $index;
                if (isset($key[$i0]) && isset($key[$i1])) {
                    $components['primes'] = [1 => $key[$i0], $key[$i1]];
                }
            }
        }
        if (isset($key['exponents'])) {
            $components['exponents'] = $key['exponents'];
        } else {
            $indices = [['dp', 'dq'], ['exponent1', 'exponent2']];
            foreach ($indices as $index) {
                list($i0, $i1) = $index;
                if (isset($key[$i0]) && isset($key[$i1])) {
                    $components['exponents'] = [1 => $key[$i0], $key[$i1]];
                }
            }
        }
        if (isset($key['coefficients'])) {
            $components['coefficients'] = $key['coefficients'];
        } else {
            foreach (['inverseq', 'q\'', 'coefficient'] as $index) {
                if (isset($key[$index])) {
                    $components['coefficients'] = [2 => $key[$index]];
                }
            }
        }
        if (!isset($components['primes'])) {
            $components['isPublicKey'] = \true;
            return $components;
        }
        if (!isset($components['exponents'])) {
            $one = new BigInteger(1);
            $temp = $components['primes'][1]->subtract($one);
            $exponents = [1 => $components['publicExponent']->modInverse($temp)];
            $temp = $components['primes'][2]->subtract($one);
            $exponents[] = $components['publicExponent']->modInverse($temp);
            $components['exponents'] = $exponents;
        }
        if (!isset($components['coefficients'])) {
            $components['coefficients'] = [2 => $components['primes'][2]->modInverse($components['primes'][1])];
        }
        foreach (['privateexponent', 'd'] as $index) {
            if (isset($key[$index])) {
                $components['privateExponent'] = $key[$index];
                break;
            }
        }
        return $components;
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
        if (!empty($password) && is_string($password)) {
            throw new UnsupportedFormatException('Raw private keys do not support encryption');
        }
        return ['e' => clone $e, 'n' => clone $n, 'd' => clone $d, 'primes' => array_map(function ($var) {
            return clone $var;
        }, $primes), 'exponents' => array_map(function ($var) {
            return clone $var;
        }, $exponents), 'coefficients' => array_map(function ($var) {
            return clone $var;
        }, $coefficients)];
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     */
    public static function savePublicKey($n, $e)
    {
        return ['e' => clone $e, 'n' => clone $n];
    }
}
