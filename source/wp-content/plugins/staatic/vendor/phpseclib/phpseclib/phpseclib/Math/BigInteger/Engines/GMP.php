<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines;

use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
class GMP extends Engine
{
    const FAST_BITWISE = \true;
    const ENGINE_DIR = 'GMP';
    public static function isValidEngine()
    {
        return extension_loaded('gmp');
    }
    public function __construct($x = 0, $base = 10)
    {
        if (!isset(static::$isValidEngine[static::class])) {
            static::$isValidEngine[static::class] = self::isValidEngine();
        }
        if (!static::$isValidEngine[static::class]) {
            throw new BadConfigurationException('GMP is not setup correctly on this system');
        }
        if ($x instanceof \GMP) {
            $this->value = $x;
            return;
        }
        $this->value = gmp_init(0);
        parent::__construct($x, $base);
    }
    protected function initialize($base)
    {
        switch (abs($base)) {
            case 256:
                $this->value = gmp_import($this->value);
                if ($this->is_negative) {
                    $this->value = -$this->value;
                }
                break;
            case 16:
                $temp = $this->is_negative ? '-0x' . $this->value : ('0x' . $this->value);
                $this->value = gmp_init($temp);
                break;
            case 10:
                $this->value = gmp_init(isset($this->value) ? $this->value : '0');
        }
    }
    public function toString()
    {
        return (string) $this->value;
    }
    public function toBits($twos_compliment = \false)
    {
        $hex = $this->toHex($twos_compliment);
        $bits = gmp_strval(gmp_init($hex, 16), 2);
        if ($this->precision > 0) {
            $bits = substr($bits, -$this->precision);
        }
        if ($twos_compliment && $this->compare(new static()) > 0 && $this->precision <= 0) {
            return '0' . $bits;
        }
        return $bits;
    }
    public function toBytes($twos_compliment = \false)
    {
        if ($twos_compliment) {
            return $this->toBytesHelper();
        }
        if (gmp_cmp($this->value, gmp_init(0)) == 0) {
            return ($this->precision > 0) ? str_repeat(chr(0), $this->precision + 1 >> 3) : '';
        }
        $temp = gmp_export($this->value);
        return ($this->precision > 0) ? substr(str_pad($temp, $this->precision >> 3, chr(0), \STR_PAD_LEFT), -($this->precision >> 3)) : ltrim($temp, chr(0));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $y
     */
    public function add($y)
    {
        $temp = new self();
        $temp->value = $this->value + $y->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $y
     */
    public function subtract($y)
    {
        $temp = new self();
        $temp->value = $this->value - $y->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $x
     */
    public function multiply($x)
    {
        $temp = new self();
        $temp->value = $this->value * $x->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $y
     */
    public function divide($y)
    {
        $quotient = new self();
        $remainder = new self();
        list($quotient->value, $remainder->value) = gmp_div_qr($this->value, $y->value);
        if (gmp_sign($remainder->value) < 0) {
            $remainder->value = $remainder->value + gmp_abs($y->value);
        }
        return [$this->normalize($quotient), $this->normalize($remainder)];
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $y
     */
    public function compare($y)
    {
        $r = gmp_cmp($this->value, $y->value);
        if ($r < -1) {
            $r = -1;
        }
        if ($r > 1) {
            $r = 1;
        }
        return $r;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $x
     */
    public function equals($x)
    {
        return $this->value == $x->value;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function modInverse($n)
    {
        $temp = new self();
        $temp->value = gmp_invert($this->value, $n->value);
        return ($temp->value === \false) ? \false : $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function extendedGCD($n)
    {
        extract(gmp_gcdext($this->value, $n->value));
        return ['gcd' => $this->normalize(new self($g)), 'x' => $this->normalize(new self($s)), 'y' => $this->normalize(new self($t))];
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function gcd($n)
    {
        $r = gmp_gcd($this->value, $n->value);
        return $this->normalize(new self($r));
    }
    public function abs()
    {
        $temp = new self();
        $temp->value = gmp_abs($this->value);
        return $temp;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $x
     */
    public function bitwise_and($x)
    {
        $temp = new self();
        $temp->value = $this->value & $x->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $x
     */
    public function bitwise_or($x)
    {
        $temp = new self();
        $temp->value = $this->value | $x->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $x
     */
    public function bitwise_xor($x)
    {
        $temp = new self();
        $temp->value = $this->value ^ $x->value;
        return $this->normalize($temp);
    }
    public function bitwise_rightShift($shift)
    {
        $temp = new self();
        $temp->value = $this->value >> $shift;
        return $this->normalize($temp);
    }
    public function bitwise_leftShift($shift)
    {
        $temp = new self();
        $temp->value = $this->value << $shift;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function modPow($e, $n)
    {
        return $this->powModOuter($e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function powMod($e, $n)
    {
        return $this->powModOuter($e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    protected function powModInner($e, $n)
    {
        $class = static::$modexpEngine[static::class];
        return $class::powModHelper($this, $e, $n);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $result
     */
    protected function normalize($result)
    {
        $result->precision = $this->precision;
        $result->bitmask = $this->bitmask;
        if ($result->bitmask !== \false) {
            $flip = $result->value < 0;
            if ($flip) {
                $result->value = -$result->value;
            }
            $result->value = $result->value & $result->bitmask->value;
            if ($flip) {
                $result->value = -$result->value;
            }
        }
        return $result;
    }
    /**
     * @param Engine $x
     * @param Engine $min
     * @param Engine $max
     */
    protected static function randomRangePrimeInner($x, $min, $max)
    {
        $p = gmp_nextprime($x->value);
        if ($p <= $max->value) {
            return new self($p);
        }
        if ($min->value != $x->value) {
            $x = new self($x->value - 1);
        }
        return self::randomRangePrime($min, $x);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $max
     */
    public static function randomRangePrime($min, $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $max
     */
    public static function randomRange($min, $max)
    {
        return self::randomRangeHelper($min, $max);
    }
    protected function make_odd()
    {
        gmp_setbit($this->value, 0);
    }
    protected function testPrimality($t)
    {
        return gmp_prob_prime($this->value, $t) != 0;
    }
    protected function rootInner($n)
    {
        $root = new self();
        $root->value = gmp_root($this->value, $n);
        return $this->normalize($root);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $n
     */
    public function pow($n)
    {
        $temp = new self();
        $temp->value = $this->value ** $n->value;
        return $this->normalize($temp);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP ...$nums
     */
    public static function min(...$nums)
    {
        return self::minHelper($nums);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP ...$nums
     */
    public static function max(...$nums)
    {
        return self::maxHelper($nums);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $max
     */
    public function between($min, $max)
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }
    public function createRecurringModuloFunction()
    {
        $temp = $this->value;
        return function (GMP $x) use ($temp) {
            return new GMP($x->value % $temp);
        };
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\GMP $r
     */
    public static function scan1divide($r)
    {
        $s = gmp_scan1($r->value, 0);
        $r->value >>= $s;
        return $s;
    }
    public function isOdd()
    {
        return gmp_testbit($this->value, 0);
    }
    public function testBit($x)
    {
        return gmp_testbit($this->value, $x);
    }
    public function isNegative()
    {
        return gmp_sign($this->value) == -1;
    }
    public function negate()
    {
        $temp = clone $this;
        $temp->value = -$this->value;
        return $temp;
    }
}
