<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;
abstract class Base extends PHP
{
    const VARIABLE = 0;
    const DATA = 1;
    public static function isValidEngine()
    {
        return static::class != __CLASS__;
    }
    /**
     * @param PHP $x
     * @param PHP $e
     * @param PHP $n
     */
    protected static function powModHelper($x, $e, $n, $class)
    {
        if (empty($e->value)) {
            $temp = new $class();
            $temp->value = [1];
            return $x->normalize($temp);
        }
        if ($e->value == [1]) {
            list(, $temp) = $x->divide($n);
            return $x->normalize($temp);
        }
        if ($e->value == [2]) {
            $temp = new $class();
            $temp->value = $class::square($x->value);
            list(, $temp) = $temp->divide($n);
            return $x->normalize($temp);
        }
        return $x->normalize(static::slidingWindow($x, $e, $n, $class));
    }
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function prepareReduce($x, $n, $class)
    {
        return static::reduce($x, $n, $class);
    }
    /**
     * @param mixed[] $x
     * @param mixed[] $y
     * @param mixed[] $n
     */
    protected static function multiplyReduce($x, $y, $n, $class)
    {
        $temp = $class::multiplyHelper($x, \false, $y, \false);
        return static::reduce($temp[self::VALUE], $n, $class);
    }
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function squareReduce($x, $n, $class)
    {
        return static::reduce($class::square($x), $n, $class);
    }
}
