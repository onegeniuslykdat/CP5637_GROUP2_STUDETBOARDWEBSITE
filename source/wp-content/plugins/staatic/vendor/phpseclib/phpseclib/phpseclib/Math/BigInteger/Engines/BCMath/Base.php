<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath;
abstract class Base extends BCMath
{
    const VARIABLE = 0;
    const DATA = 1;
    public static function isValidEngine()
    {
        return static::class != __CLASS__;
    }
    /**
     * @param BCMath $x
     * @param BCMath $e
     * @param BCMath $n
     */
    protected static function powModHelper($x, $e, $n, $class)
    {
        if (empty($e->value)) {
            $temp = new $class();
            $temp->value = '1';
            return $x->normalize($temp);
        }
        return $x->normalize(static::slidingWindow($x, $e, $n, $class));
    }
    protected static function prepareReduce($x, $n, $class)
    {
        return static::reduce($x, $n);
    }
    protected static function multiplyReduce($x, $y, $n, $class)
    {
        return static::reduce(bcmul($x, $y), $n);
    }
    protected static function squareReduce($x, $n, $class)
    {
        return static::reduce(bcmul($x, $x), $n);
    }
}
