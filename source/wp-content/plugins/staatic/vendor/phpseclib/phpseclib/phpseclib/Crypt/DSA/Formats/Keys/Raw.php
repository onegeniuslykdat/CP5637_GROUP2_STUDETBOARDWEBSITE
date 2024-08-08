<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Keys;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class Raw
{
    public static function load($key, $password = '')
    {
        if (!is_array($key)) {
            throw new UnexpectedValueException('Key should be a array - not a ' . gettype($key));
        }
        switch (\true) {
            case !isset($key['p']) || !isset($key['q']) || !isset($key['g']):
            case !$key['p'] instanceof BigInteger:
            case !$key['q'] instanceof BigInteger:
            case !$key['g'] instanceof BigInteger:
            case !isset($key['x']) && !isset($key['y']):
            case isset($key['x']) && !$key['x'] instanceof BigInteger:
            case isset($key['y']) && !$key['y'] instanceof BigInteger:
                throw new UnexpectedValueException('Key appears to be malformed');
        }
        $options = ['p' => 1, 'q' => 1, 'g' => 1, 'x' => 1, 'y' => 1];
        return array_intersect_key($key, $options);
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     * @param BigInteger $x
     */
    public static function savePrivateKey($p, $q, $g, $y, $x, $password = '')
    {
        return compact('p', 'q', 'g', 'y', 'x');
    }
    /**
     * @param BigInteger $p
     * @param BigInteger $q
     * @param BigInteger $g
     * @param BigInteger $y
     */
    public static function savePublicKey($p, $q, $g, $y)
    {
        return compact('p', 'q', 'g', 'y');
    }
}
