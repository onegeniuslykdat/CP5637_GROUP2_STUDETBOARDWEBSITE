<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath;
abstract class BuiltIn extends BCMath
{
    /**
     * @param BCMath $x
     * @param BCMath $e
     * @param BCMath $n
     */
    protected static function powModHelper($x, $e, $n)
    {
        $temp = new BCMath();
        $temp->value = bcpowmod($x->value, $e->value, $n->value);
        return $x->normalize($temp);
    }
}
