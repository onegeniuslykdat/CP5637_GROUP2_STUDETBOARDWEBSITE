<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines;

use JsonSerializable;
use InvalidArgumentException;
use ReturnTypeWillChange;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class Engine implements JsonSerializable
{
    const PRIMES = [3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 101, 103, 107, 109, 113, 127, 131, 137, 139, 149, 151, 157, 163, 167, 173, 179, 181, 191, 193, 197, 199, 211, 223, 227, 229, 233, 239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293, 307, 311, 313, 317, 331, 337, 347, 349, 353, 359, 367, 373, 379, 383, 389, 397, 401, 409, 419, 421, 431, 433, 439, 443, 449, 457, 461, 463, 467, 479, 487, 491, 499, 503, 509, 521, 523, 541, 547, 557, 563, 569, 571, 577, 587, 593, 599, 601, 607, 613, 617, 619, 631, 641, 643, 647, 653, 659, 661, 673, 677, 683, 691, 701, 709, 719, 727, 733, 739, 743, 751, 757, 761, 769, 773, 787, 797, 809, 811, 821, 823, 827, 829, 839, 853, 857, 859, 863, 877, 881, 883, 887, 907, 911, 919, 929, 937, 941, 947, 953, 967, 971, 977, 983, 991, 997];
    protected static $zero = [];
    protected static $one = [];
    protected static $two = [];
    protected static $modexpEngine;
    protected static $isValidEngine;
    protected $value;
    protected $is_negative;
    protected $precision = -1;
    protected $bitmask = \false;
    protected $reduce;
    protected $hex;
    public function __construct($x = 0, $base = 10)
    {
        if (!array_key_exists(static::class, static::$zero)) {
            static::$zero[static::class] = null;
            static::$zero[static::class] = new static(0);
            static::$one[static::class] = new static(1);
            static::$two[static::class] = new static(2);
        }
        if (empty($x) && (abs($base) != 256 || $x !== '0')) {
            return;
        }
        switch ($base) {
            case -256:
            case 256:
                if ($base == -256 && ord($x[0]) & 0x80) {
                    $this->value = ~$x;
                    $this->is_negative = \true;
                } else {
                    $this->value = $x;
                    $this->is_negative = \false;
                }
                $this->initialize($base);
                if ($this->is_negative) {
                    $temp = $this->add(new static('-1'));
                    $this->value = $temp->value;
                }
                break;
            case -16:
            case 16:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = \true;
                    $x = substr($x, 1);
                }
                $x = preg_replace('#^(?:0x)?([A-Fa-f0-9]*).*#s', '$1', $x);
                $is_negative = \false;
                if ($base < 0 && hexdec($x[0]) >= 8) {
                    $this->is_negative = $is_negative = \true;
                    $x = Strings::bin2hex(~Strings::hex2bin($x));
                }
                $this->value = $x;
                $this->initialize($base);
                if ($is_negative) {
                    $temp = $this->add(new static('-1'));
                    $this->value = $temp->value;
                }
                break;
            case -10:
            case 10:
                $this->value = preg_replace('#(?<!^)(?:-).*|(?<=^|-)0*|[^-0-9].*#s', '', $x);
                if (!strlen($this->value) || $this->value == '-') {
                    $this->value = '0';
                }
                $this->initialize($base);
                break;
            case -2:
            case 2:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = \true;
                    $x = substr($x, 1);
                }
                $x = preg_replace('#^([01]*).*#s', '$1', $x);
                $temp = new static(Strings::bits2bin($x), 128 * $base);
                $this->value = $temp->value;
                if ($temp->is_negative) {
                    $this->is_negative = \true;
                }
                break;
            default:
        }
    }
    public static function setModExpEngine($engine)
    {
        $fqengine = 'Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\\' . static::ENGINE_DIR . '\\' . $engine;
        if (!class_exists($fqengine) || !method_exists($fqengine, 'isValidEngine')) {
            throw new InvalidArgumentException("{$engine} is not a valid engine");
        }
        if (!$fqengine::isValidEngine()) {
            throw new BadConfigurationException("{$engine} is not setup correctly on this system");
        }
        static::$modexpEngine[static::class] = $fqengine;
    }
    protected function toBytesHelper()
    {
        $comparison = $this->compare(new static());
        if ($comparison == 0) {
            return ($this->precision > 0) ? str_repeat(chr(0), $this->precision + 1 >> 3) : '';
        }
        $temp = ($comparison < 0) ? $this->add(new static(1)) : $this;
        $bytes = $temp->toBytes();
        if (!strlen($bytes)) {
            $bytes = chr(0);
        }
        if (ord($bytes[0]) & 0x80) {
            $bytes = chr(0) . $bytes;
        }
        return ($comparison < 0) ? ~$bytes : $bytes;
    }
    public function toHex($twos_compliment = \false)
    {
        return Strings::bin2hex($this->toBytes($twos_compliment));
    }
    public function toBits($twos_compliment = \false)
    {
        $hex = $this->toBytes($twos_compliment);
        $bits = Strings::bin2bits($hex);
        $result = ($this->precision > 0) ? substr($bits, -$this->precision) : ltrim($bits, '0');
        if ($twos_compliment && $this->compare(new static()) > 0 && $this->precision <= 0) {
            return '0' . $result;
        }
        return $result;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $n
     */
    protected function modInverseHelper($n)
    {
        $n = $n->abs();
        if ($this->compare(static::$zero[static::class]) < 0) {
            $temp = $this->abs();
            $temp = $temp->modInverse($n);
            return $this->normalize($n->subtract($temp));
        }
        extract($this->extendedGCD($n));
        if (!$gcd->equals(static::$one[static::class])) {
            return \false;
        }
        $x = ($x->compare(static::$zero[static::class]) < 0) ? $x->add($n) : $x;
        return ($this->compare(static::$zero[static::class]) < 0) ? $this->normalize($n->subtract($x)) : $this->normalize($x);
    }
    public function __sleep()
    {
        $this->hex = $this->toHex(\true);
        $vars = ['hex'];
        if ($this->precision > 0) {
            $vars[] = 'precision';
        }
        return $vars;
    }
    public function __wakeup()
    {
        $temp = new static($this->hex, -16);
        $this->value = $temp->value;
        $this->is_negative = $temp->is_negative;
        if ($this->precision > 0) {
            $this->setPrecision($this->precision);
        }
    }
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = ['hex' => $this->toHex(\true)];
        if ($this->precision > 0) {
            $result['precision'] = $this->precision;
        }
        return $result;
    }
    public function __toString()
    {
        return $this->toString();
    }
    public function __debugInfo()
    {
        $result = ['value' => '0x' . $this->toHex(\true), 'engine' => basename(static::class)];
        return ($this->precision > 0) ? $result + ['precision' => $this->precision] : $result;
    }
    public function setPrecision($bits)
    {
        if ($bits < 1) {
            $this->precision = -1;
            $this->bitmask = \false;
            return;
        }
        $this->precision = $bits;
        $this->bitmask = static::setBitmask($bits);
        $temp = $this->normalize($this);
        $this->value = $temp->value;
    }
    public function getPrecision()
    {
        return $this->precision;
    }
    protected static function setBitmask($bits)
    {
        return new static(chr((1 << ($bits & 0x7)) - 1) . str_repeat(chr(0xff), $bits >> 3), 256);
    }
    public function bitwise_not()
    {
        $temp = $this->toBytes();
        if ($temp == '') {
            return $this->normalize(static::$zero[static::class]);
        }
        $pre_msb = decbin(ord($temp[0]));
        $temp = ~$temp;
        $msb = decbin(ord($temp[0]));
        if (strlen($msb) == 8) {
            $msb = substr($msb, strpos($msb, '0'));
        }
        $temp[0] = chr(bindec($msb));
        $current_bits = strlen($pre_msb) + 8 * strlen($temp) - 8;
        $new_bits = $this->precision - $current_bits;
        if ($new_bits <= 0) {
            return $this->normalize(new static($temp, 256));
        }
        $leading_ones = chr((1 << ($new_bits & 0x7)) - 1) . str_repeat(chr(0xff), $new_bits >> 3);
        self::base256_lshift($leading_ones, $current_bits);
        $temp = str_pad($temp, strlen($leading_ones), chr(0), \STR_PAD_LEFT);
        return $this->normalize(new static($leading_ones | $temp, 256));
    }
    protected static function base256_lshift(&$x, $shift)
    {
        if ($shift == 0) {
            return;
        }
        $num_bytes = $shift >> 3;
        $shift &= 7;
        $carry = 0;
        for ($i = strlen($x) - 1; $i >= 0; --$i) {
            $temp = ord($x[$i]) << $shift | $carry;
            $x[$i] = chr($temp);
            $carry = $temp >> 8;
        }
        $carry = ($carry != 0) ? chr($carry) : '';
        $x = $carry . $x . str_repeat(chr(0), $num_bytes);
    }
    public function bitwise_leftRotate($shift)
    {
        $bits = $this->toBytes();
        if ($this->precision > 0) {
            $precision = $this->precision;
            if (static::FAST_BITWISE) {
                $mask = $this->bitmask->toBytes();
            } else {
                $mask = $this->bitmask->subtract(new static(1));
                $mask = $mask->toBytes();
            }
        } else {
            $temp = ord($bits[0]);
            for ($i = 0; $temp >> $i; ++$i) {
            }
            $precision = 8 * strlen($bits) - 8 + $i;
            $mask = chr((1 << ($precision & 0x7)) - 1) . str_repeat(chr(0xff), $precision >> 3);
        }
        if ($shift < 0) {
            $shift += $precision;
        }
        $shift %= $precision;
        if (!$shift) {
            return clone $this;
        }
        $left = $this->bitwise_leftShift($shift);
        $left = $left->bitwise_and(new static($mask, 256));
        $right = $this->bitwise_rightShift($precision - $shift);
        $result = static::FAST_BITWISE ? $left->bitwise_or($right) : $left->add($right);
        return $this->normalize($result);
    }
    public function bitwise_rightRotate($shift)
    {
        return $this->bitwise_leftRotate(-$shift);
    }
    public static function minMaxBits($bits)
    {
        $bytes = $bits >> 3;
        $min = str_repeat(chr(0), $bytes);
        $max = str_repeat(chr(0xff), $bytes);
        $msb = $bits & 7;
        if ($msb) {
            $min = chr(1 << $msb - 1) . $min;
            $max = chr((1 << $msb) - 1) . $max;
        } else {
            $min[0] = chr(0x80);
        }
        return ['min' => new static($min, 256), 'max' => new static($max, 256)];
    }
    public function getLength()
    {
        return strlen($this->toBits());
    }
    public function getLengthInBytes()
    {
        return (int) ceil($this->getLength() / 8);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $n
     */
    protected function powModOuter($e, $n)
    {
        $n = ($this->bitmask !== \false && $this->bitmask->compare($n) < 0) ? $this->bitmask : $n->abs();
        if ($e->compare(new static()) < 0) {
            $e = $e->abs();
            $temp = $this->modInverse($n);
            if ($temp === \false) {
                return \false;
            }
            return $this->normalize($temp->powModInner($e, $n));
        }
        if ($this->compare($n) > 0) {
            list(, $temp) = $this->divide($n);
            return $temp->powModInner($e, $n);
        }
        return $this->powModInner($e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $x
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $n
     */
    protected static function slidingWindow($x, $e, $n, $class)
    {
        static $window_ranges = [7, 25, 81, 241, 673, 1793];
        $e_bits = $e->toBits();
        $e_length = strlen($e_bits);
        for ($i = 0, $window_size = 1; $i < count($window_ranges) && $e_length > $window_ranges[$i]; ++$window_size, ++$i) {
        }
        $n_value = $n->value;
        if (method_exists(static::class, 'generateCustomReduction')) {
            static::generateCustomReduction($n, $class);
        }
        $powers = [];
        $powers[1] = static::prepareReduce($x->value, $n_value, $class);
        $powers[2] = static::squareReduce($powers[1], $n_value, $class);
        $temp = 1 << $window_size - 1;
        for ($i = 1; $i < $temp; ++$i) {
            $i2 = $i << 1;
            $powers[$i2 + 1] = static::multiplyReduce($powers[$i2 - 1], $powers[2], $n_value, $class);
        }
        $result = new $class(1);
        $result = static::prepareReduce($result->value, $n_value, $class);
        for ($i = 0; $i < $e_length;) {
            if (!$e_bits[$i]) {
                $result = static::squareReduce($result, $n_value, $class);
                ++$i;
            } else {
                for ($j = $window_size - 1; $j > 0; --$j) {
                    if (!empty($e_bits[$i + $j])) {
                        break;
                    }
                }
                for ($k = 0; $k <= $j; ++$k) {
                    $result = static::squareReduce($result, $n_value, $class);
                }
                $result = static::multiplyReduce($result, $powers[bindec(substr($e_bits, $i, $j + 1))], $n_value, $class);
                $i += $j + 1;
            }
        }
        $temp = new $class();
        $temp->value = static::reduce($result, $n_value, $class);
        return $temp;
    }
    public static function random($size)
    {
        extract(static::minMaxBits($size));
        return static::randomRange($min, $max);
    }
    public static function randomPrime($size)
    {
        extract(static::minMaxBits($size));
        return static::randomRangePrime($min, $max);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $max
     */
    protected static function randomRangePrimeOuter($min, $max)
    {
        $compare = $max->compare($min);
        if (!$compare) {
            return $min->isPrime() ? $min : \false;
        } elseif ($compare < 0) {
            $temp = $max;
            $max = $min;
            $min = $temp;
        }
        $length = $max->getLength();
        if ($length > 8196) {
            throw new RuntimeException("Generation of random prime numbers larger than 8196 has been disabled ({$length})");
        }
        $x = static::randomRange($min, $max);
        return static::randomRangePrimeInner($x, $min, $max);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $max
     */
    protected static function randomRangeHelper($min, $max)
    {
        $compare = $max->compare($min);
        if (!$compare) {
            return $min;
        } elseif ($compare < 0) {
            $temp = $max;
            $max = $min;
            $min = $temp;
        }
        if (!isset(static::$one[static::class])) {
            static::$one[static::class] = new static(1);
        }
        $max = $max->subtract($min->subtract(static::$one[static::class]));
        $size = strlen(ltrim($max->toBytes(), chr(0)));
        $random_max = new static(chr(1) . str_repeat("\x00", $size), 256);
        $random = new static(Random::string($size), 256);
        list($max_multiple) = $random_max->divide($max);
        $max_multiple = $max_multiple->multiply($max);
        while ($random->compare($max_multiple) >= 0) {
            $random = $random->subtract($max_multiple);
            $random_max = $random_max->subtract($max_multiple);
            $random = $random->bitwise_leftShift(8);
            $random = $random->add(new static(Random::string(1), 256));
            $random_max = $random_max->bitwise_leftShift(8);
            list($max_multiple) = $random_max->divide($max);
            $max_multiple = $max_multiple->multiply($max);
        }
        list(, $random) = $random->divide($max);
        return $random->add($min);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $x
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $max
     */
    protected static function randomRangePrimeInner($x, $min, $max)
    {
        if (!isset(static::$two[static::class])) {
            static::$two[static::class] = new static('2');
        }
        $x->make_odd();
        if ($x->compare($max) > 0) {
            if ($min->equals($max)) {
                return \false;
            }
            $x = clone $min;
            $x->make_odd();
        }
        $initial_x = clone $x;
        while (\true) {
            if ($x->isPrime()) {
                return $x;
            }
            $x = $x->add(static::$two[static::class]);
            if ($x->compare($max) > 0) {
                $x = clone $min;
                if ($x->equals(static::$two[static::class])) {
                    return $x;
                }
                $x->make_odd();
            }
            if ($x->equals($initial_x)) {
                return \false;
            }
        }
    }
    protected function setupIsPrime()
    {
        $length = $this->getLengthInBytes();
        if ($length >= 163) {
            $t = 2;
        } else if ($length >= 106) {
            $t = 3;
        } else if ($length >= 81) {
            $t = 4;
        } else if ($length >= 68) {
            $t = 5;
        } else if ($length >= 56) {
            $t = 6;
        } else if ($length >= 50) {
            $t = 7;
        } else if ($length >= 43) {
            $t = 8;
        } else if ($length >= 37) {
            $t = 9;
        } else if ($length >= 31) {
            $t = 12;
        } else if ($length >= 25) {
            $t = 15;
        } else if ($length >= 18) {
            $t = 18;
        } else {
            $t = 27;
        }
        return $t;
    }
    protected function testPrimality($t)
    {
        if (!$this->testSmallPrimes()) {
            return \false;
        }
        $n = clone $this;
        $n_1 = $n->subtract(static::$one[static::class]);
        $n_2 = $n->subtract(static::$two[static::class]);
        $r = clone $n_1;
        $s = static::scan1divide($r);
        for ($i = 0; $i < $t; ++$i) {
            $a = static::randomRange(static::$two[static::class], $n_2);
            $y = $a->modPow($r, $n);
            if (!$y->equals(static::$one[static::class]) && !$y->equals($n_1)) {
                for ($j = 1; $j < $s && !$y->equals($n_1); ++$j) {
                    $y = $y->modPow(static::$two[static::class], $n);
                    if ($y->equals(static::$one[static::class])) {
                        return \false;
                    }
                }
                if (!$y->equals($n_1)) {
                    return \false;
                }
            }
        }
        return \true;
    }
    public function isPrime($t = \false)
    {
        $length = $this->getLength();
        if ($length > 8196) {
            throw new RuntimeException("Primality testing is not supported for numbers larger than 8196 bits ({$length})");
        }
        if (!$t) {
            $t = $this->setupIsPrime();
        }
        return $this->testPrimality($t);
    }
    protected function rootHelper($n)
    {
        if ($n < 1) {
            return clone static::$zero[static::class];
        }
        if ($this->compare(static::$one[static::class]) < 0) {
            return clone static::$zero[static::class];
        }
        if ($this->compare(static::$two[static::class]) < 0) {
            return clone static::$one[static::class];
        }
        return $this->rootInner($n);
    }
    protected function rootInner($n)
    {
        $n = new static($n);
        $g = static::$two[static::class];
        while ($g->pow($n)->compare($this) < 0) {
            $g = $g->multiply(static::$two[static::class]);
        }
        if ($g->pow($n)->equals($this) > 0) {
            $root = $g;
            return $this->normalize($root);
        }
        $og = $g;
        $g = $g->divide(static::$two[static::class])[0];
        $step = $og->subtract($g)->divide(static::$two[static::class])[0];
        $g = $g->add($step);
        while ($step->compare(static::$one[static::class]) == 1) {
            $guess = $g->pow($n);
            $step = $step->divide(static::$two[static::class])[0];
            $comp = $guess->compare($this);
            switch ($comp) {
                case -1:
                    $g = $g->add($step);
                    break;
                case 1:
                    $g = $g->subtract($step);
                    break;
                case 0:
                    $root = $g;
                    break 2;
            }
        }
        if ($comp == 1) {
            $g = $g->subtract($step);
        }
        $root = $g;
        return $this->normalize($root);
    }
    public function root($n = 2)
    {
        return $this->rootHelper($n);
    }
    /**
     * @param mixed[] $nums
     */
    protected static function minHelper($nums)
    {
        if (count($nums) == 1) {
            return $nums[0];
        }
        $min = $nums[0];
        for ($i = 1; $i < count($nums); $i++) {
            $min = ($min->compare($nums[$i]) > 0) ? $nums[$i] : $min;
        }
        return $min;
    }
    /**
     * @param mixed[] $nums
     */
    protected static function maxHelper($nums)
    {
        if (count($nums) == 1) {
            return $nums[0];
        }
        $max = $nums[0];
        for ($i = 1; $i < count($nums); $i++) {
            $max = ($max->compare($nums[$i]) < 0) ? $nums[$i] : $max;
        }
        return $max;
    }
    public function createRecurringModuloFunction()
    {
        $class = static::class;
        $fqengine = (!method_exists(static::$modexpEngine[static::class], 'reduce')) ? '\Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\\' . static::ENGINE_DIR . '\DefaultEngine' : static::$modexpEngine[static::class];
        if (method_exists($fqengine, 'generateCustomReduction')) {
            $func = $fqengine::generateCustomReduction($this, static::class);
            return eval('return function(' . static::class . ' $x) use ($func, $class) {
                $r = new $class();
                $r->value = $func($x->value);
                return $r;
            };');
        }
        $n = $this->value;
        return eval('return function(' . static::class . ' $x) use ($n, $fqengine, $class) {
            $r = new $class();
            $r->value = $fqengine::reduce($x->value, $n, $class);
            return $r;
        };');
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $n
     */
    protected function extendedGCDHelper($n)
    {
        $u = clone $this;
        $v = clone $n;
        $one = new static(1);
        $zero = new static();
        $a = clone $one;
        $b = clone $zero;
        $c = clone $zero;
        $d = clone $one;
        while (!$v->equals($zero)) {
            list($q) = $u->divide($v);
            $temp = $u;
            $u = $v;
            $v = $temp->subtract($v->multiply($q));
            $temp = $a;
            $a = $c;
            $c = $temp->subtract($a->multiply($q));
            $temp = $b;
            $b = $d;
            $d = $temp->subtract($b->multiply($q));
        }
        return ['gcd' => $u, 'x' => $a, 'y' => $b];
    }
    public function bitwise_split($split)
    {
        if ($split < 1) {
            throw new RuntimeException('Offset must be greater than 1');
        }
        $mask = static::$one[static::class]->bitwise_leftShift($split)->subtract(static::$one[static::class]);
        $num = clone $this;
        $vals = [];
        while (!$num->equals(static::$zero[static::class])) {
            $vals[] = $num->bitwise_and($mask);
            $num = $num->bitwise_rightShift($split);
        }
        return array_reverse($vals);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $x
     */
    protected function bitwiseAndHelper($x)
    {
        $left = $this->toBytes(\true);
        $right = $x->toBytes(\true);
        $length = max(strlen($left), strlen($right));
        $left = str_pad($left, $length, chr(0), \STR_PAD_LEFT);
        $right = str_pad($right, $length, chr(0), \STR_PAD_LEFT);
        return $this->normalize(new static($left & $right, -256));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $x
     */
    protected function bitwiseOrHelper($x)
    {
        $left = $this->toBytes(\true);
        $right = $x->toBytes(\true);
        $length = max(strlen($left), strlen($right));
        $left = str_pad($left, $length, chr(0), \STR_PAD_LEFT);
        $right = str_pad($right, $length, chr(0), \STR_PAD_LEFT);
        return $this->normalize(new static($left | $right, -256));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine $x
     */
    protected function bitwiseXorHelper($x)
    {
        $left = $this->toBytes(\true);
        $right = $x->toBytes(\true);
        $length = max(strlen($left), strlen($right));
        $left = str_pad($left, $length, chr(0), \STR_PAD_LEFT);
        $right = str_pad($right, $length, chr(0), \STR_PAD_LEFT);
        return $this->normalize(new static($left ^ $right, -256));
    }
}
