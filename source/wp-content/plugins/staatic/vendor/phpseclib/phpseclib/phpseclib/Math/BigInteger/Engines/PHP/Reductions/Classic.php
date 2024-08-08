<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Base;
abstract class Classic extends Base
{
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
        list(, $temp) = $lhs->divide($rhs);
        return $temp->value;
    }
}
