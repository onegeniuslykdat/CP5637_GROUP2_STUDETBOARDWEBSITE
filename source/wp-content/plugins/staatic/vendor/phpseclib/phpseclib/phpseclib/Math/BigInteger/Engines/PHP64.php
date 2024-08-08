<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines;

class PHP64 extends PHP
{
    const BASE = 31;
    const BASE_FULL = 0x80000000;
    const MAX_DIGIT = 0x7fffffff;
    const MSB = 0x40000000;
    const MAX10 = 1000000000;
    const MAX10LEN = 9;
    const MAX_DIGIT2 = 4611686018427387904;
    protected function initialize($base)
    {
        if ($base != 256 && $base != -256) {
            return parent::initialize($base);
        }
        $val = $this->value;
        $this->value = [];
        $vals =& $this->value;
        $i = strlen($val);
        if (!$i) {
            return;
        }
        while (\true) {
            $i -= 4;
            if ($i < 0) {
                if ($i == -4) {
                    break;
                }
                $val = substr($val, 0, 4 + $i);
                $val = str_pad($val, 4, "\x00", \STR_PAD_LEFT);
                if ($val == "\x00\x00\x00\x00") {
                    break;
                }
                $i = 0;
            }
            list(, $digit) = unpack('N', substr($val, $i, 4));
            $step = count($vals) & 7;
            if (!$step) {
                $digit &= static::MAX_DIGIT;
                $i++;
            } else {
                $shift = 8 - $step;
                $digit >>= $shift;
                $shift = 32 - $shift;
                $digit &= (1 << $shift) - 1;
                $temp = ($i > 0) ? ord($val[$i - 1]) : 0;
                $digit |= $temp << $shift & 0x7f000000;
            }
            $vals[] = $digit;
        }
        while (end($vals) === 0) {
            array_pop($vals);
        }
        reset($vals);
    }
    public static function isValidEngine()
    {
        return \PHP_INT_SIZE >= 8 && !self::testJITOnWindows();
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $y
     */
    public function add($y)
    {
        $temp = self::addHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
        return $this->convertToObj($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $y
     */
    public function subtract($y)
    {
        $temp = self::subtractHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
        return $this->convertToObj($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $y
     */
    public function multiply($y)
    {
        $temp = self::multiplyHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
        return $this->convertToObj($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $y
     */
    public function divide($y)
    {
        return $this->divideHelper($y);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function modInverse($n)
    {
        return $this->modInverseHelper($n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function extendedGCD($n)
    {
        return $this->extendedGCDHelper($n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function gcd($n)
    {
        return $this->extendedGCD($n)['gcd'];
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $x
     */
    public function bitwise_and($x)
    {
        return $this->bitwiseAndHelper($x);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $x
     */
    public function bitwise_or($x)
    {
        return $this->bitwiseOrHelper($x);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $x
     */
    public function bitwise_xor($x)
    {
        return $this->bitwiseXorHelper($x);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $y
     */
    public function compare($y)
    {
        return parent::compareHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $x
     */
    public function equals($x)
    {
        return $this->value === $x->value && $this->is_negative == $x->is_negative;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function modPow($e, $n)
    {
        return $this->powModOuter($e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function powMod($e, $n)
    {
        return $this->powModOuter($e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $max
     */
    public static function randomRangePrime($min, $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $max
     */
    public static function randomRange($min, $max)
    {
        return self::randomRangeHelper($min, $max);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $n
     */
    public function pow($n)
    {
        return $this->powHelper($n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 ...$nums
     */
    public static function min(...$nums)
    {
        return self::minHelper($nums);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 ...$nums
     */
    public static function max(...$nums)
    {
        return self::maxHelper($nums);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP64 $max
     */
    public function between($min, $max)
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }
}
