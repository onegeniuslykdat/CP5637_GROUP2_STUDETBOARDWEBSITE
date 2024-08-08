<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines;

use Exception;
use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\DefaultEngine;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
abstract class PHP extends Engine
{
    const VALUE = 0;
    const SIGN = 1;
    const KARATSUBA_CUTOFF = 25;
    const FAST_BITWISE = \true;
    const ENGINE_DIR = 'PHP';
    public function __construct($x = 0, $base = 10)
    {
        if (!isset(static::$isValidEngine[static::class])) {
            static::$isValidEngine[static::class] = static::isValidEngine();
        }
        if (!static::$isValidEngine[static::class]) {
            throw new BadConfigurationException(static::class . ' is not setup correctly on this system');
        }
        $this->value = [];
        parent::__construct($x, $base);
    }
    protected function initialize($base)
    {
        switch (abs($base)) {
            case 16:
                $x = (strlen($this->value) & 1) ? '0' . $this->value : $this->value;
                $temp = new static(Strings::hex2bin($x), 256);
                $this->value = $temp->value;
                break;
            case 10:
                $temp = new static();
                $multiplier = new static();
                $multiplier->value = [static::MAX10];
                $x = $this->value;
                if ($x[0] == '-') {
                    $this->is_negative = \true;
                    $x = substr($x, 1);
                }
                $x = str_pad($x, strlen($x) + (static::MAX10LEN - 1) * strlen($x) % static::MAX10LEN, 0, \STR_PAD_LEFT);
                while (strlen($x)) {
                    $temp = $temp->multiply($multiplier);
                    $temp = $temp->add(new static($this->int2bytes(substr($x, 0, static::MAX10LEN)), 256));
                    $x = substr($x, static::MAX10LEN);
                }
                $this->value = $temp->value;
        }
    }
    protected function pad($str)
    {
        $length = strlen($str);
        $pad = 4 - strlen($str) % 4;
        return str_pad($str, $length + $pad, "\x00", \STR_PAD_LEFT);
    }
    public function toString()
    {
        if (!count($this->value)) {
            return '0';
        }
        $temp = clone $this;
        $temp->bitmask = \false;
        $temp->is_negative = \false;
        $divisor = new static();
        $divisor->value = [static::MAX10];
        $result = '';
        while (count($temp->value)) {
            list($temp, $mod) = $temp->divide($divisor);
            $result = str_pad(isset($mod->value[0]) ? $mod->value[0] : '', static::MAX10LEN, '0', \STR_PAD_LEFT) . $result;
        }
        $result = ltrim($result, '0');
        if (empty($result)) {
            $result = '0';
        }
        if ($this->is_negative) {
            $result = '-' . $result;
        }
        return $result;
    }
    public function toBytes($twos_compliment = \false)
    {
        if ($twos_compliment) {
            return $this->toBytesHelper();
        }
        if (!count($this->value)) {
            return ($this->precision > 0) ? str_repeat(chr(0), $this->precision + 1 >> 3) : '';
        }
        $result = $this->bitwise_small_split(8);
        $result = implode('', array_map('chr', $result));
        return ($this->precision > 0) ? str_pad(substr($result, -($this->precision + 7 >> 3)), $this->precision + 7 >> 3, chr(0), \STR_PAD_LEFT) : $result;
    }
    /**
     * @param mixed[] $x_value
     * @param mixed[] $y_value
     */
    protected static function addHelper($x_value, $x_negative, $y_value, $y_negative)
    {
        $x_size = count($x_value);
        $y_size = count($y_value);
        if ($x_size == 0) {
            return [self::VALUE => $y_value, self::SIGN => $y_negative];
        } elseif ($y_size == 0) {
            return [self::VALUE => $x_value, self::SIGN => $x_negative];
        }
        if ($x_negative != $y_negative) {
            if ($x_value == $y_value) {
                return [self::VALUE => [], self::SIGN => \false];
            }
            $temp = self::subtractHelper($x_value, \false, $y_value, \false);
            $temp[self::SIGN] = (self::compareHelper($x_value, \false, $y_value, \false) > 0) ? $x_negative : $y_negative;
            return $temp;
        }
        if ($x_size < $y_size) {
            $size = $x_size;
            $value = $y_value;
        } else {
            $size = $y_size;
            $value = $x_value;
        }
        $value[count($value)] = 0;
        $carry = 0;
        for ($i = 0, $j = 1; $j < $size; $i += 2, $j += 2) {
            $sum = ($x_value[$j] + $y_value[$j]) * static::BASE_FULL + $x_value[$i] + $y_value[$i] + $carry;
            $carry = $sum >= static::MAX_DIGIT2;
            $sum = $carry ? $sum - static::MAX_DIGIT2 : $sum;
            $temp = (static::BASE === 26) ? intval($sum / 0x4000000) : ($sum >> 31);
            $value[$i] = (int) ($sum - static::BASE_FULL * $temp);
            $value[$j] = $temp;
        }
        if ($j == $size) {
            $sum = $x_value[$i] + $y_value[$i] + $carry;
            $carry = $sum >= static::BASE_FULL;
            $value[$i] = $carry ? $sum - static::BASE_FULL : $sum;
            ++$i;
        }
        if ($carry) {
            for (; $value[$i] == static::MAX_DIGIT; ++$i) {
                $value[$i] = 0;
            }
            ++$value[$i];
        }
        return [self::VALUE => self::trim($value), self::SIGN => $x_negative];
    }
    /**
     * @param mixed[] $x_value
     * @param mixed[] $y_value
     */
    public static function subtractHelper($x_value, $x_negative, $y_value, $y_negative)
    {
        $x_size = count($x_value);
        $y_size = count($y_value);
        if ($x_size == 0) {
            return [self::VALUE => $y_value, self::SIGN => !$y_negative];
        } elseif ($y_size == 0) {
            return [self::VALUE => $x_value, self::SIGN => $x_negative];
        }
        if ($x_negative != $y_negative) {
            $temp = self::addHelper($x_value, \false, $y_value, \false);
            $temp[self::SIGN] = $x_negative;
            return $temp;
        }
        $diff = self::compareHelper($x_value, $x_negative, $y_value, $y_negative);
        if (!$diff) {
            return [self::VALUE => [], self::SIGN => \false];
        }
        if (!$x_negative && $diff < 0 || $x_negative && $diff > 0) {
            $temp = $x_value;
            $x_value = $y_value;
            $y_value = $temp;
            $x_negative = !$x_negative;
            $x_size = count($x_value);
            $y_size = count($y_value);
        }
        $carry = 0;
        for ($i = 0, $j = 1; $j < $y_size; $i += 2, $j += 2) {
            $sum = ($x_value[$j] - $y_value[$j]) * static::BASE_FULL + $x_value[$i] - $y_value[$i] - $carry;
            $carry = $sum < 0;
            $sum = $carry ? $sum + static::MAX_DIGIT2 : $sum;
            $temp = (static::BASE === 26) ? intval($sum / 0x4000000) : ($sum >> 31);
            $x_value[$i] = (int) ($sum - static::BASE_FULL * $temp);
            $x_value[$j] = $temp;
        }
        if ($j == $y_size) {
            $sum = $x_value[$i] - $y_value[$i] - $carry;
            $carry = $sum < 0;
            $x_value[$i] = $carry ? $sum + static::BASE_FULL : $sum;
            ++$i;
        }
        if ($carry) {
            for (; !$x_value[$i]; ++$i) {
                $x_value[$i] = static::MAX_DIGIT;
            }
            --$x_value[$i];
        }
        return [self::VALUE => self::trim($x_value), self::SIGN => $x_negative];
    }
    /**
     * @param mixed[] $x_value
     * @param mixed[] $y_value
     */
    protected static function multiplyHelper($x_value, $x_negative, $y_value, $y_negative)
    {
        $x_length = count($x_value);
        $y_length = count($y_value);
        if (!$x_length || !$y_length) {
            return [self::VALUE => [], self::SIGN => \false];
        }
        return [self::VALUE => (min($x_length, $y_length) < 2 * self::KARATSUBA_CUTOFF) ? self::trim(self::regularMultiply($x_value, $y_value)) : self::trim(self::karatsuba($x_value, $y_value)), self::SIGN => $x_negative != $y_negative];
    }
    private static function karatsuba(array $x_value, array $y_value)
    {
        $m = min(count($x_value) >> 1, count($y_value) >> 1);
        if ($m < self::KARATSUBA_CUTOFF) {
            return self::regularMultiply($x_value, $y_value);
        }
        $x1 = array_slice($x_value, $m);
        $x0 = array_slice($x_value, 0, $m);
        $y1 = array_slice($y_value, $m);
        $y0 = array_slice($y_value, 0, $m);
        $z2 = self::karatsuba($x1, $y1);
        $z0 = self::karatsuba($x0, $y0);
        $z1 = self::addHelper($x1, \false, $x0, \false);
        $temp = self::addHelper($y1, \false, $y0, \false);
        $z1 = self::karatsuba($z1[self::VALUE], $temp[self::VALUE]);
        $temp = self::addHelper($z2, \false, $z0, \false);
        $z1 = self::subtractHelper($z1, \false, $temp[self::VALUE], \false);
        $z2 = array_merge(array_fill(0, 2 * $m, 0), $z2);
        $z1[self::VALUE] = array_merge(array_fill(0, $m, 0), $z1[self::VALUE]);
        $xy = self::addHelper($z2, \false, $z1[self::VALUE], $z1[self::SIGN]);
        $xy = self::addHelper($xy[self::VALUE], $xy[self::SIGN], $z0, \false);
        return $xy[self::VALUE];
    }
    /**
     * @param mixed[] $x_value
     * @param mixed[] $y_value
     */
    protected static function regularMultiply($x_value, $y_value)
    {
        $x_length = count($x_value);
        $y_length = count($y_value);
        if (!$x_length || !$y_length) {
            return [];
        }
        $product_value = self::array_repeat(0, $x_length + $y_length);
        $carry = 0;
        for ($j = 0; $j < $x_length; ++$j) {
            $temp = $x_value[$j] * $y_value[0] + $carry;
            $carry = (static::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
            $product_value[$j] = (int) ($temp - static::BASE_FULL * $carry);
        }
        $product_value[$j] = $carry;
        for ($i = 1; $i < $y_length; ++$i) {
            $carry = 0;
            for ($j = 0, $k = $i; $j < $x_length; ++$j, ++$k) {
                $temp = $product_value[$k] + $x_value[$j] * $y_value[$i] + $carry;
                $carry = (static::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
                $product_value[$k] = (int) ($temp - static::BASE_FULL * $carry);
            }
            $product_value[$k] = $carry;
        }
        return $product_value;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $y
     */
    protected function divideHelper($y)
    {
        if (count($y->value) == 1) {
            list($q, $r) = $this->divide_digit($this->value, $y->value[0]);
            $quotient = new static();
            $remainder = new static();
            $quotient->value = $q;
            $remainder->value = [$r];
            $quotient->is_negative = $this->is_negative != $y->is_negative;
            return [$this->normalize($quotient), $this->normalize($remainder)];
        }
        $x = clone $this;
        $y = clone $y;
        $x_sign = $x->is_negative;
        $y_sign = $y->is_negative;
        $x->is_negative = $y->is_negative = \false;
        $diff = $x->compare($y);
        if (!$diff) {
            $temp = new static();
            $temp->value = [1];
            $temp->is_negative = $x_sign != $y_sign;
            return [$this->normalize($temp), $this->normalize(static::$zero[static::class])];
        }
        if ($diff < 0) {
            if ($x_sign) {
                $x = $y->subtract($x);
            }
            return [$this->normalize(static::$zero[static::class]), $this->normalize($x)];
        }
        $msb = $y->value[count($y->value) - 1];
        for ($shift = 0; !($msb & static::MSB); ++$shift) {
            $msb <<= 1;
        }
        $x->lshift($shift);
        $y->lshift($shift);
        $y_value =& $y->value;
        $x_max = count($x->value) - 1;
        $y_max = count($y->value) - 1;
        $quotient = new static();
        $quotient_value =& $quotient->value;
        $quotient_value = self::array_repeat(0, $x_max - $y_max + 1);
        static $temp, $lhs, $rhs;
        if (!isset($temp)) {
            $temp = new static();
            $lhs = new static();
            $rhs = new static();
        }
        if (static::class != get_class($temp)) {
            $temp = new static();
            $lhs = new static();
            $rhs = new static();
        }
        $temp_value =& $temp->value;
        $rhs_value =& $rhs->value;
        $temp_value = array_merge(self::array_repeat(0, $x_max - $y_max), $y_value);
        while ($x->compare($temp) >= 0) {
            ++$quotient_value[$x_max - $y_max];
            $x = $x->subtract($temp);
            $x_max = count($x->value) - 1;
        }
        for ($i = $x_max; $i >= $y_max + 1; --$i) {
            $x_value =& $x->value;
            $x_window = [isset($x_value[$i]) ? $x_value[$i] : 0, isset($x_value[$i - 1]) ? $x_value[$i - 1] : 0, isset($x_value[$i - 2]) ? $x_value[$i - 2] : 0];
            $y_window = [$y_value[$y_max], ($y_max > 0) ? $y_value[$y_max - 1] : 0];
            $q_index = $i - $y_max - 1;
            if ($x_window[0] == $y_window[0]) {
                $quotient_value[$q_index] = static::MAX_DIGIT;
            } else {
                $quotient_value[$q_index] = self::safe_divide($x_window[0] * static::BASE_FULL + $x_window[1], $y_window[0]);
            }
            $temp_value = [$y_window[1], $y_window[0]];
            $lhs->value = [$quotient_value[$q_index]];
            $lhs = $lhs->multiply($temp);
            $rhs_value = [$x_window[2], $x_window[1], $x_window[0]];
            while ($lhs->compare($rhs) > 0) {
                --$quotient_value[$q_index];
                $lhs->value = [$quotient_value[$q_index]];
                $lhs = $lhs->multiply($temp);
            }
            $adjust = self::array_repeat(0, $q_index);
            $temp_value = [$quotient_value[$q_index]];
            $temp = $temp->multiply($y);
            $temp_value =& $temp->value;
            if (count($temp_value)) {
                $temp_value = array_merge($adjust, $temp_value);
            }
            $x = $x->subtract($temp);
            if ($x->compare(static::$zero[static::class]) < 0) {
                $temp_value = array_merge($adjust, $y_value);
                $x = $x->add($temp);
                --$quotient_value[$q_index];
            }
            $x_max = count($x_value) - 1;
        }
        $x->rshift($shift);
        $quotient->is_negative = $x_sign != $y_sign;
        if ($x_sign) {
            $y->rshift($shift);
            $x = $y->subtract($x);
        }
        return [$this->normalize($quotient), $this->normalize($x)];
    }
    private static function divide_digit(array $dividend, $divisor)
    {
        $carry = 0;
        $result = [];
        for ($i = count($dividend) - 1; $i >= 0; --$i) {
            $temp = static::BASE_FULL * $carry + $dividend[$i];
            $result[$i] = self::safe_divide($temp, $divisor);
            $carry = (int) ($temp - $divisor * $result[$i]);
        }
        return [$result, $carry];
    }
    private static function safe_divide($x, $y)
    {
        if (static::BASE === 26) {
            return (int) ($x / $y);
        }
        return ($x - $x % $y) / $y;
    }
    /**
     * @param mixed[] $arr
     */
    protected function convertToObj($arr)
    {
        $result = new static();
        $result->value = $arr[self::VALUE];
        $result->is_negative = $arr[self::SIGN];
        return $this->normalize($result);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $result
     */
    protected function normalize($result)
    {
        $result->precision = $this->precision;
        $result->bitmask = $this->bitmask;
        $value =& $result->value;
        if (!count($value)) {
            $result->is_negative = \false;
            return $result;
        }
        $value = static::trim($value);
        if (!empty($result->bitmask->value)) {
            $length = min(count($value), count($result->bitmask->value));
            $value = array_slice($value, 0, $length);
            for ($i = 0; $i < $length; ++$i) {
                $value[$i] = $value[$i] & $result->bitmask->value[$i];
            }
            $value = static::trim($value);
        }
        return $result;
    }
    /**
     * @param mixed[] $x_value
     * @param mixed[] $y_value
     */
    protected static function compareHelper($x_value, $x_negative, $y_value, $y_negative)
    {
        if ($x_negative != $y_negative) {
            return (!$x_negative && $y_negative) ? 1 : -1;
        }
        $result = $x_negative ? -1 : 1;
        if (count($x_value) != count($y_value)) {
            return (count($x_value) > count($y_value)) ? $result : -$result;
        }
        $size = max(count($x_value), count($y_value));
        $x_value = array_pad($x_value, $size, 0);
        $y_value = array_pad($y_value, $size, 0);
        for ($i = count($x_value) - 1; $i >= 0; --$i) {
            if ($x_value[$i] != $y_value[$i]) {
                return ($x_value[$i] > $y_value[$i]) ? $result : -$result;
            }
        }
        return 0;
    }
    public function abs()
    {
        $temp = new static();
        $temp->value = $this->value;
        return $temp;
    }
    /**
     * @param mixed[] $value
     */
    protected static function trim($value)
    {
        for ($i = count($value) - 1; $i >= 0; --$i) {
            if ($value[$i]) {
                break;
            }
            unset($value[$i]);
        }
        return $value;
    }
    public function bitwise_rightShift($shift)
    {
        $temp = new static();
        $temp->value = $this->value;
        $temp->rshift($shift);
        return $this->normalize($temp);
    }
    public function bitwise_leftShift($shift)
    {
        $temp = new static();
        $temp->value = $this->value;
        $temp->lshift($shift);
        return $this->normalize($temp);
    }
    private static function int2bytes($x)
    {
        return ltrim(pack('N', $x), chr(0));
    }
    protected static function array_repeat($input, $multiplier)
    {
        return $multiplier ? array_fill(0, $multiplier, $input) : [];
    }
    protected function lshift($shift)
    {
        if ($shift == 0) {
            return;
        }
        $num_digits = (int) ($shift / static::BASE);
        $shift %= static::BASE;
        $shift = 1 << $shift;
        $carry = 0;
        for ($i = 0; $i < count($this->value); ++$i) {
            $temp = $this->value[$i] * $shift + $carry;
            $carry = (static::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
            $this->value[$i] = (int) ($temp - $carry * static::BASE_FULL);
        }
        if ($carry) {
            $this->value[count($this->value)] = $carry;
        }
        while ($num_digits--) {
            array_unshift($this->value, 0);
        }
    }
    protected function rshift($shift)
    {
        if ($shift == 0) {
            return;
        }
        $num_digits = (int) ($shift / static::BASE);
        $shift %= static::BASE;
        $carry_shift = static::BASE - $shift;
        $carry_mask = (1 << $shift) - 1;
        if ($num_digits) {
            $this->value = array_slice($this->value, $num_digits);
        }
        $carry = 0;
        for ($i = count($this->value) - 1; $i >= 0; --$i) {
            $temp = $this->value[$i] >> $shift | $carry;
            $carry = ($this->value[$i] & $carry_mask) << $carry_shift;
            $this->value[$i] = $temp;
        }
        $this->value = static::trim($this->value);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $n
     */
    protected function powModInner($e, $n)
    {
        try {
            $class = static::$modexpEngine[static::class];
            return $class::powModHelper($this, $e, $n, static::class);
        } catch (Exception $err) {
            return DefaultEngine::powModHelper($this, $e, $n, static::class);
        }
    }
    /**
     * @param mixed[] $x
     */
    protected static function square($x)
    {
        return (count($x) < 2 * self::KARATSUBA_CUTOFF) ? self::trim(self::baseSquare($x)) : self::trim(self::karatsubaSquare($x));
    }
    /**
     * @param mixed[] $value
     */
    protected static function baseSquare($value)
    {
        if (empty($value)) {
            return [];
        }
        $square_value = self::array_repeat(0, 2 * count($value));
        for ($i = 0, $max_index = count($value) - 1; $i <= $max_index; ++$i) {
            $i2 = $i << 1;
            $temp = $square_value[$i2] + $value[$i] * $value[$i];
            $carry = (static::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
            $square_value[$i2] = (int) ($temp - static::BASE_FULL * $carry);
            for ($j = $i + 1, $k = $i2 + 1; $j <= $max_index; ++$j, ++$k) {
                $temp = $square_value[$k] + 2 * $value[$j] * $value[$i] + $carry;
                $carry = (static::BASE === 26) ? intval($temp / 0x4000000) : ($temp >> 31);
                $square_value[$k] = (int) ($temp - static::BASE_FULL * $carry);
            }
            $square_value[$i + $max_index + 1] = $carry;
        }
        return $square_value;
    }
    /**
     * @param mixed[] $value
     */
    protected static function karatsubaSquare($value)
    {
        $m = count($value) >> 1;
        if ($m < self::KARATSUBA_CUTOFF) {
            return self::baseSquare($value);
        }
        $x1 = array_slice($value, $m);
        $x0 = array_slice($value, 0, $m);
        $z2 = self::karatsubaSquare($x1);
        $z0 = self::karatsubaSquare($x0);
        $z1 = self::addHelper($x1, \false, $x0, \false);
        $z1 = self::karatsubaSquare($z1[self::VALUE]);
        $temp = self::addHelper($z2, \false, $z0, \false);
        $z1 = self::subtractHelper($z1, \false, $temp[self::VALUE], \false);
        $z2 = array_merge(array_fill(0, 2 * $m, 0), $z2);
        $z1[self::VALUE] = array_merge(array_fill(0, $m, 0), $z1[self::VALUE]);
        $xx = self::addHelper($z2, \false, $z1[self::VALUE], $z1[self::SIGN]);
        $xx = self::addHelper($xx[self::VALUE], $xx[self::SIGN], $z0, \false);
        return $xx[self::VALUE];
    }
    protected function make_odd()
    {
        $this->value[0] |= 1;
    }
    protected function testSmallPrimes()
    {
        if ($this->value == [1]) {
            return \false;
        }
        if ($this->value == [2]) {
            return \true;
        }
        if (~$this->value[0] & 1) {
            return \false;
        }
        $value = $this->value;
        foreach (static::PRIMES as $prime) {
            list(, $r) = self::divide_digit($value, $prime);
            if (!$r) {
                return count($value) == 1 && $value[0] == $prime;
            }
        }
        return \true;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $r
     */
    public static function scan1divide($r)
    {
        $r_value =& $r->value;
        for ($i = 0, $r_length = count($r_value); $i < $r_length; ++$i) {
            $temp = ~$r_value[$i] & static::MAX_DIGIT;
            for ($j = 1; $temp >> $j & 1; ++$j) {
            }
            if ($j <= static::BASE) {
                break;
            }
        }
        $s = static::BASE * $i + $j;
        $r->rshift($s);
        return $s;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP $n
     */
    protected function powHelper($n)
    {
        if ($n->compare(static::$zero[static::class]) == 0) {
            return new static(1);
        }
        $temp = clone $this;
        while (!$n->equals(static::$one[static::class])) {
            $temp = $temp->multiply($this);
            $n = $n->subtract(static::$one[static::class]);
        }
        return $temp;
    }
    public function isOdd()
    {
        return (bool) ($this->value[0] & 1);
    }
    public function testBit($x)
    {
        $digit = (int) floor($x / static::BASE);
        $bit = $x % static::BASE;
        if (!isset($this->value[$digit])) {
            return \false;
        }
        return (bool) ($this->value[$digit] & 1 << $bit);
    }
    public function isNegative()
    {
        return $this->is_negative;
    }
    public function negate()
    {
        $temp = clone $this;
        $temp->is_negative = !$temp->is_negative;
        return $temp;
    }
    public function bitwise_split($split)
    {
        if ($split < 1) {
            throw new RuntimeException('Offset must be greater than 1');
        }
        $width = (int) ($split / static::BASE);
        if (!$width) {
            $arr = $this->bitwise_small_split($split);
            return array_map(function ($digit) {
                $temp = new static();
                $temp->value = ($digit != 0) ? [$digit] : [];
                return $temp;
            }, $arr);
        }
        $vals = [];
        $val = $this->value;
        $i = $overflow = 0;
        $len = count($val);
        while ($i < $len) {
            $digit = [];
            if (!$overflow) {
                $digit = array_slice($val, $i, $width);
                $i += $width;
                $overflow = $split % static::BASE;
                if ($overflow) {
                    $mask = (1 << $overflow) - 1;
                    $temp = isset($val[$i]) ? $val[$i] : 0;
                    $digit[] = $temp & $mask;
                }
            } else {
                $remaining = static::BASE - $overflow;
                $tempsplit = $split - $remaining;
                $tempwidth = (int) ($tempsplit / static::BASE + 1);
                $digit = array_slice($val, $i, $tempwidth);
                $i += $tempwidth;
                $tempoverflow = $tempsplit % static::BASE;
                if ($tempoverflow) {
                    $tempmask = (1 << $tempoverflow) - 1;
                    $temp = isset($val[$i]) ? $val[$i] : 0;
                    $digit[] = $temp & $tempmask;
                }
                $newbits = 0;
                for ($j = count($digit) - 1; $j >= 0; $j--) {
                    $temp = $digit[$j] & $mask;
                    $digit[$j] = $digit[$j] >> $overflow | $newbits << $remaining;
                    $newbits = $temp;
                }
                $overflow = $tempoverflow;
                $mask = $tempmask;
            }
            $temp = new static();
            $temp->value = static::trim($digit);
            $vals[] = $temp;
        }
        return array_reverse($vals);
    }
    private function bitwise_small_split($split)
    {
        $vals = [];
        $val = $this->value;
        $mask = (1 << $split) - 1;
        $i = $overflow = 0;
        $len = count($val);
        $val[] = 0;
        $remaining = static::BASE;
        while ($i != $len) {
            $digit = $val[$i] & $mask;
            $val[$i] >>= $split;
            if (!$overflow) {
                $remaining -= $split;
                $overflow = ($split <= $remaining) ? 0 : ($split - $remaining);
                if (!$remaining) {
                    $i++;
                    $remaining = static::BASE;
                    $overflow = 0;
                }
            } elseif (++$i != $len) {
                $tempmask = (1 << $overflow) - 1;
                $digit |= ($val[$i] & $tempmask) << $remaining;
                $val[$i] >>= $overflow;
                $remaining = static::BASE - $overflow;
                $overflow = ($split <= $remaining) ? 0 : ($split - $remaining);
            }
            $vals[] = $digit;
        }
        while ($vals[count($vals) - 1] == 0) {
            unset($vals[count($vals) - 1]);
        }
        return array_reverse($vals);
    }
    protected static function testJITOnWindows()
    {
        if (strtoupper(substr(\PHP_OS, 0, 3)) === 'WIN' && function_exists('opcache_get_status') && \PHP_VERSION_ID < 80213 && !defined('Staatic\Vendor\PHPSECLIB_ALLOW_JIT')) {
            $status = opcache_get_status();
            if ($status && isset($status['jit']) && $status['jit']['enabled'] && $status['jit']['on']) {
                return \true;
            }
        }
        return \false;
    }
    public function getLength()
    {
        $max = count($this->value) - 1;
        return ($max != -1) ? $max * static::BASE + intval(ceil(log($this->value[$max] + 1, 2))) : 0;
    }
}
