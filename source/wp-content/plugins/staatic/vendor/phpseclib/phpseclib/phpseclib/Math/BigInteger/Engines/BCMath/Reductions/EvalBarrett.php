<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath;
use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath\Base;
abstract class EvalBarrett extends Base
{
    private static $custom_reduction;
    protected static function reduce($n, $m)
    {
        $inline = self::$custom_reduction;
        return $inline($n);
    }
    /**
     * @param BCMath $m
     */
    protected static function generateCustomReduction($m, $class)
    {
        $m_length = strlen($m);
        if ($m_length < 5) {
            $code = 'return bcmod($x, $n);';
            eval('$func = function ($n) { ' . $code . '};');
            self::$custom_reduction = $func;
            return;
        }
        $lhs = '1' . str_repeat('0', $m_length + ($m_length >> 1));
        $u = bcdiv($lhs, $m, 0);
        $m1 = bcsub($lhs, bcmul($u, $m));
        $cutoff = $m_length + ($m_length >> 1);
        $m = "'{$m}'";
        $u = "'{$u}'";
        $m1 = "'{$m1}'";
        $code = '
            $lsd = substr($n, -' . $cutoff . ');
            $msd = substr($n, 0, -' . $cutoff . ');

            $temp = bcmul($msd, ' . $m1 . ');
            $n = bcadd($lsd, $temp);

            $temp = substr($n, 0, ' . (-$m_length + 1) . ');
            $temp = bcmul($temp, ' . $u . ');
            $temp = substr($temp, 0, ' . (-($m_length >> 1) - 1) . ');
            $temp = bcmul($temp, ' . $m . ');

            $result = bcsub($n, $temp);

            if ($result[0] == \'-\') {
                $temp = \'1' . str_repeat('0', $m_length + 1) . '\';
                $result = bcadd($result, $temp);
            }

            while (bccomp($result, ' . $m . ') >= 0) {
                $result = bcsub($result, ' . $m . ');
            }

            return $result;';
        eval('$func = function ($n) { ' . $code . '};');
        self::$custom_reduction = $func;
        return $func;
    }
}
