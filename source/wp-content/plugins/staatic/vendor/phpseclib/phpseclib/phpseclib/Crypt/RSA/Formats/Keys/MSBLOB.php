<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class MSBLOB
{
    const PRIVATEKEYBLOB = 0x7;
    const PUBLICKEYBLOB = 0x6;
    const PUBLICKEYBLOBEX = 0xa;
    const CALG_RSA_KEYX = 0xa400;
    const CALG_RSA_SIGN = 0x2400;
    const RSA1 = 0x31415352;
    const RSA2 = 0x32415352;
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        $key = Strings::base64_decode($key);
        if (!is_string($key)) {
            throw new UnexpectedValueException('Base64 decoding produced an error');
        }
        if (strlen($key) < 20) {
            throw new UnexpectedValueException('Key appears to be malformed');
        }
        extract(unpack('atype/aversion/vreserved/Valgo', Strings::shift($key, 8)));
        switch (ord($type)) {
            case self::PUBLICKEYBLOB:
            case self::PUBLICKEYBLOBEX:
                $publickey = \true;
                break;
            case self::PRIVATEKEYBLOB:
                $publickey = \false;
                break;
            default:
                throw new UnexpectedValueException('Key appears to be malformed');
        }
        $components = ['isPublicKey' => $publickey];
        switch ($algo) {
            case self::CALG_RSA_KEYX:
            case self::CALG_RSA_SIGN:
                break;
            default:
                throw new UnexpectedValueException('Key appears to be malformed');
        }
        extract(unpack('Vmagic/Vbitlen/a4pubexp', Strings::shift($key, 12)));
        switch ($magic) {
            case self::RSA2:
                $components['isPublicKey'] = \false;
            case self::RSA1:
                break;
            default:
                throw new UnexpectedValueException('Key appears to be malformed');
        }
        $baseLength = $bitlen / 16;
        if (strlen($key) != 2 * $baseLength && strlen($key) != 9 * $baseLength) {
            throw new UnexpectedValueException('Key appears to be malformed');
        }
        $components[$components['isPublicKey'] ? 'publicExponent' : 'privateExponent'] = new BigInteger(strrev($pubexp), 256);
        $components['modulus'] = new BigInteger(strrev(Strings::shift($key, $bitlen / 8)), 256);
        if ($publickey) {
            return $components;
        }
        $components['isPublicKey'] = \false;
        $components['primes'] = [1 => new BigInteger(strrev(Strings::shift($key, $bitlen / 16)), 256)];
        $components['primes'][] = new BigInteger(strrev(Strings::shift($key, $bitlen / 16)), 256);
        $components['exponents'] = [1 => new BigInteger(strrev(Strings::shift($key, $bitlen / 16)), 256)];
        $components['exponents'][] = new BigInteger(strrev(Strings::shift($key, $bitlen / 16)), 256);
        $components['coefficients'] = [2 => new BigInteger(strrev(Strings::shift($key, $bitlen / 16)), 256)];
        if (isset($components['privateExponent'])) {
            $components['publicExponent'] = $components['privateExponent'];
        }
        $components['privateExponent'] = new BigInteger(strrev(Strings::shift($key, $bitlen / 8)), 256);
        return $components;
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param BigInteger $d
     * @param mixed[] $primes
     * @param mixed[] $exponents
     * @param mixed[] $coefficients
     */
    public static function savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients, $password = '')
    {
        if (count($primes) != 2) {
            throw new InvalidArgumentException('MSBLOB does not support multi-prime RSA keys');
        }
        if (!empty($password) && is_string($password)) {
            throw new UnsupportedFormatException('MSBLOB private keys do not support encryption');
        }
        $n = strrev($n->toBytes());
        $e = str_pad(strrev($e->toBytes()), 4, "\x00");
        $key = pack('aavV', chr(self::PRIVATEKEYBLOB), chr(2), 0, self::CALG_RSA_KEYX);
        $key .= pack('VVa*', self::RSA2, 8 * strlen($n), $e);
        $key .= $n;
        $key .= strrev($primes[1]->toBytes());
        $key .= strrev($primes[2]->toBytes());
        $key .= strrev($exponents[1]->toBytes());
        $key .= strrev($exponents[2]->toBytes());
        $key .= strrev($coefficients[2]->toBytes());
        $key .= strrev($d->toBytes());
        return Strings::base64_encode($key);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     */
    public static function savePublicKey($n, $e)
    {
        $n = strrev($n->toBytes());
        $e = str_pad(strrev($e->toBytes()), 4, "\x00");
        $key = pack('aavV', chr(self::PUBLICKEYBLOB), chr(2), 0, self::CALG_RSA_KEYX);
        $key .= pack('VVa*', self::RSA1, 8 * strlen($n), $e);
        $key .= $n;
        return Strings::base64_encode($key);
    }
}
