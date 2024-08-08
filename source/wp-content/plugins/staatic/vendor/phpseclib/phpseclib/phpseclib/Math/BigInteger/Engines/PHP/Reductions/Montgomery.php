<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Montgomery as Progenitor;
abstract class Montgomery extends Progenitor
{
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function prepareReduce($x, $n, $class)
    {
        $lhs = new $class();
        $lhs->value = array_merge(self::array_repeat(0, count($n)), $x);
        $rhs = new $class();
        $rhs->value = $n;
        list(, $temp) = $lhs->divide($rhs);
        return $temp->value;
    }
    /**
     * @param mixed[] $x
     * @param mixed[] $n
     */
    protected static function reduce($x, $n, $class)
    {
        static $cache = [self::VARIABLE => [], self::DATA => []];
        if (($key = array_search($n, $cache[self::VARIABLE])) === \false) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $x;
            $cache[self::DATA][] = self::modInverse67108864($n, $class);
        }
        $k = count($n);
        $result = [self::VALUE => $x];
        for ($i = 0; $i < $k; ++$i) {
            $temp = $result[self::VALUE][$i] * $cache[self::DATA][$key];
            $temp = $temp - $class::BASE_FULL * (($class::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $class::regularMultiply([$temp], $n);
            $temp = array_merge(self::array_repeat(0, $i), $temp);
            $result = $class::addHelper($result[self::VALUE], \false, $temp, \false);
        }
        $result[self::VALUE] = array_slice($result[self::VALUE], $k);
        if (self::compareHelper($result, \false, $n, \false) >= 0) {
            $result = $class::subtractHelper($result[self::VALUE], \false, $n, \false);
        }
        return $result[self::VALUE];
    }
    /**
     * @param mixed[] $x
     */
    protected static function modInverse67108864($x, $class)
    {
        $x = -$x[0];
        $result = $x & 0x3;
        $result = $result * (2 - $x * $result) & 0xf;
        $result = $result * (2 - ($x & 0xff) * $result) & 0xff;
        $result = $result * (2 - ($x & 0xffff) * $result & 0xffff) & 0xffff;
        $result = ($class::BASE == 26) ? fmod($result * (2 - fmod($x * $result, $class::BASE_FULL)), $class::BASE_FULL) : ($result * (2 - $x * $result % $class::BASE_FULL) % $class::BASE_FULL);
        return $result & $class::MAX_DIGIT;
    }
}
