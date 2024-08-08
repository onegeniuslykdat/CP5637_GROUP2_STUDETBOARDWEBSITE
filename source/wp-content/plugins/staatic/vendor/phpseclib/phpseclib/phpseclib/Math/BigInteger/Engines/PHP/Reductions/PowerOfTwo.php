<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Base;
abstract class PowerOfTwo extends Base
{
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function prepareReduce($x, $n, $class)
    {
        return self::reduce($x, $n, $class);
    }
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function reduce($x, $n, $class)
    {
        $lhs = new $class();
        $lhs->value = $x;
        $rhs = new $class();
        $rhs->value = $n;
        $temp = new $class();
        $temp->value = [1];
        $result = $lhs->bitwise_and($rhs->subtract($temp));
        return $result->value;
    }
}
