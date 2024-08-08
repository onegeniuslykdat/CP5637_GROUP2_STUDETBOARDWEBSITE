<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP;
abstract class DefaultEngine extends GMP
{
    /**
     * @param GMP $x
     * @param GMP $e
     * @param GMP $n
     */
    protected static function powModHelper($x, $e, $n)
    {
        $temp = new GMP();
        $temp->value = gmp_powm($x->value, $e->value, $n->value);
        return $x->normalize($temp);
    }
}
