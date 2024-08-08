<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\BCMath\Base;
abstract class Barrett extends Base
{
    const VARIABLE = 0;
    const DATA = 1;
    protected static function reduce($n, $m)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        $m_length = strlen($m);
        if (strlen($n) >= 2 * $m_length) {
            return bcmod($n, $m);
        }
        if ($m_length < 5) {
            return self::regularBarrett($n, $m);
        }
        if (($key = array_search($m, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $m;
            $lhs = '1' . str_repeat('0', $m_length + ($m_length >> 1));
            $u = bcdiv($lhs, $m, 0);
            $m1 = bcsub($lhs, bcmul($u, $m));
            $cache[self::DATA][] = ['u' => $u, 'm1' => $m1];
        } else {
            extract($cache[self::DATA][$key]);
        }
        $cutoff = $m_length + ($m_length >> 1);
        $lsd = substr($n, -$cutoff);
        $msd = substr($n, 0, -$cutoff);
        $temp = bcmul($msd, $m1);
        $n = bcadd($lsd, $temp);
        $temp = substr($n, 0, -$m_length + 1);
        $temp = bcmul($temp, $u);
        $temp = substr($temp, 0, -($m_length >> 1) - 1);
        $temp = bcmul($temp, $m);
        $result = bcsub($n, $temp);
        if ($result[0] == '-') {
            $temp = '1' . str_repeat('0', $m_length + 1);
            $result = bcadd($result, $temp);
        }
        while (bccomp($result, $m) >= 0) {
            $result = bcsub($result, $m);
        }
        return $result;
    }
    private static function regularBarrett($x, $n)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        $n_length = strlen($n);
        if (strlen($x) > 2 * $n_length) {
            return bcmod($x, $n);
        }
        if (($key = array_search($n, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $n;
            $lhs = '1' . str_repeat('0', 2 * $n_length);
            $cache[self::DATA][] = bcdiv($lhs, $n, 0);
        }
        $temp = substr($x, 0, -$n_length + 1);
        $temp = bcmul($temp, $cache[self::DATA][$key]);
        $temp = substr($temp, 0, -$n_length - 1);
        $r1 = substr($x, -$n_length - 1);
        $r2 = substr(bcmul($temp, $n), -$n_length - 1);
        $result = bcsub($r1, $r2);
        if ($result[0] == '-') {
            $q = '1' . str_repeat('0', $n_length + 1);
            $result = bcadd($result, $q);
        }
        while (bccomp($result, $n) >= 0) {
            $result = bcsub($result, $n);
        }
        return $result;
    }
}
