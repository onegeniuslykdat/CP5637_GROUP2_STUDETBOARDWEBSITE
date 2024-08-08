<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;
use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Base;
abstract class Barrett extends Base
{
    /**
     * @param mixed[] $n
     * @param mixed[] $m
     */
    protected static function reduce($n, $m, $class)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        $m_length = count($m);
        if (count($n) >= 2 * $m_length) {
            $lhs = new $class();
            $rhs = new $class();
            $lhs->value = $n;
            $rhs->value = $m;
            list(, $temp) = $lhs->divide($rhs);
            return $temp->value;
        }
        if ($m_length < 5) {
            return self::regularBarrett($n, $m, $class);
        }
        if (($key = array_search($m, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $m;
            $lhs = new $class();
            $lhs_value =& $lhs->value;
            $lhs_value = self::array_repeat(0, $m_length + ($m_length >> 1));
            $lhs_value[] = 1;
            $rhs = new $class();
            $rhs->value = $m;
            list($u, $m1) = $lhs->divide($rhs);
            $u = $u->value;
            $m1 = $m1->value;
            $cache[self::DATA][] = ['u' => $u, 'm1' => $m1];
        } else {
            extract($cache[self::DATA][$key]);
        }
        $cutoff = $m_length + ($m_length >> 1);
        $lsd = array_slice($n, 0, $cutoff);
        $msd = array_slice($n, $cutoff);
        $lsd = self::trim($lsd);
        $temp = $class::multiplyHelper($msd, \false, $m1, \false);
        $n = $class::addHelper($lsd, \false, $temp[self::VALUE], \false);
        $temp = array_slice($n[self::VALUE], $m_length - 1);
        $temp = $class::multiplyHelper($temp, \false, $u, \false);
        $temp = array_slice($temp[self::VALUE], ($m_length >> 1) + 1);
        $temp = $class::multiplyHelper($temp, \false, $m, \false);
        $result = $class::subtractHelper($n[self::VALUE], \false, $temp[self::VALUE], \false);
        while (self::compareHelper($result[self::VALUE], $result[self::SIGN], $m, \false) >= 0) {
            $result = $class::subtractHelper($result[self::VALUE], $result[self::SIGN], $m, \false);
        }
        return $result[self::VALUE];
    }
    private static function regularBarrett(array $x, array $n, $class)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        $n_length = count($n);
        if (count($x) > 2 * $n_length) {
            $lhs = new $class();
            $rhs = new $class();
            $lhs->value = $x;
            $rhs->value = $n;
            list(, $temp) = $lhs->divide($rhs);
            return $temp->value;
        }
        if (($key = array_search($n, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $n;
            $lhs = new $class();
            $lhs_value =& $lhs->value;
            $lhs_value = self::array_repeat(0, 2 * $n_length);
            $lhs_value[] = 1;
            $rhs = new $class();
            $rhs->value = $n;
            list($temp, ) = $lhs->divide($rhs);
            $cache[self::DATA][] = $temp->value;
        }
        $temp = array_slice($x, $n_length - 1);
        $temp = $class::multiplyHelper($temp, \false, $cache[self::DATA][$key], \false);
        $temp = array_slice($temp[self::VALUE], $n_length + 1);
        $result = array_slice($x, 0, $n_length + 1);
        $temp = self::multiplyLower($temp, \false, $n, \false, $n_length + 1, $class);
        if (self::compareHelper($result, \false, $temp[self::VALUE], $temp[self::SIGN]) < 0) {
            $corrector_value = self::array_repeat(0, $n_length + 1);
            $corrector_value[count($corrector_value)] = 1;
            $result = $class::addHelper($result, \false, $corrector_value, \false);
            $result = $result[self::VALUE];
        }
        $result = $class::subtractHelper($result, \false, $temp[self::VALUE], $temp[self::SIGN]);
        while (self::compareHelper($result[self::VALUE], $result[self::SIGN], $n, \false) > 0) {
            $result = $class::subtractHelper($result[self::VALUE], $result[self::SIGN], $n, \false);
        }
        return $result[self::VALUE];
    }
    private static function multiplyLower(array $x_value, $x_negative, array $y_value, $y_negative, $stop, $class)
    {
        $x_length = count($x_value);
        $y_length = count($y_value);
        if (!$x_length || !$y_length) {
            return [self::VALUE => [], self::SIGN => \false];
        }
        if ($x_length < $y_length) {
            $temp = $x_value;
            $x_value = $y_value;
            $y_value = $temp;
            $x_length = count($x_value);
            $y_length = count($y_value);
        }
        $product_value = self::array_repeat(0, $x_length + $y_length);
        $carry = 0;
        for ($j = 0; $j < $x_length; ++$j) {
            $temp = $x_value[$j] * $y_value[0] + $carry;
            $carry = ($class::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
            $product_value[$j] = (int) ($temp - $class::BASE_FULL * $carry);
        }
        if ($j < $stop) {
            $product_value[$j] = $carry;
        }
        for ($i = 1; $i < $y_length; ++$i) {
            $carry = 0;
            for ($j = 0, $k = $i; $j < $x_length && $k < $stop; ++$j, ++$k) {
                $temp = $product_value[$k] + $x_value[$j] * $y_value[$i] + $carry;
                $carry = ($class::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
                $product_value[$k] = (int) ($temp - $class::BASE_FULL * $carry);
            }
            if ($k < $stop) {
                $product_value[$k] = $carry;
            }
        }
        return [self::VALUE => self::trim($product_value), self::SIGN => $x_negative != $y_negative];
    }
}
