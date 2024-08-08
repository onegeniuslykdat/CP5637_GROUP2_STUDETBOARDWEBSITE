<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;
abstract class MontgomeryMult extends Montgomery
{
    /**
     * @param mixed[] $x
     * @param mixed[] $y
     * @param mixed[] $m
     */
    public static function multiplyReduce($x, $y, $m, $class)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        if (($key = array_search($m, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $m;
            $cache[self::DATA][] = self::modInverse67108864($m, $class);
        }
        $n = max(count($x), count($y), count($m));
        $x = array_pad($x, $n, 0);
        $y = array_pad($y, $n, 0);
        $m = array_pad($m, $n, 0);
        $a = [self::VALUE => self::array_repeat(0, $n + 1)];
        for ($i = 0; $i < $n; ++$i) {
            $temp = $a[self::VALUE][0] + $x[$i] * $y[0];
            $temp = $temp - $class::BASE_FULL * (($class::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $temp * $cache[self::DATA][$key];
            $temp = $temp - $class::BASE_FULL * (($class::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $class::addHelper($class::regularMultiply([$x[$i]], $y), \false, $class::regularMultiply([$temp], $m), \false);
            $a = $class::addHelper($a[self::VALUE], \false, $temp[self::VALUE], \false);
            $a[self::VALUE] = array_slice($a[self::VALUE], 1);
        }
        if (self::compareHelper($a[self::VALUE], \false, $m, \false) >= 0) {
            $a = $class::subtractHelper($a[self::VALUE], \false, $m, \false);
        }
        return $a[self::VALUE];
    }
}
