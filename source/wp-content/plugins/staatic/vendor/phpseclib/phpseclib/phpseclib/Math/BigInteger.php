<?php

namespace Staatic\Vendor\phpseclib3\Math;

use JsonSerializable;
use InvalidArgumentException;
use Exception;
use UnexpectedValueException;
use ReturnTypeWillChange;
use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\Engine;
class BigInteger implements JsonSerializable
{
    private static $mainEngine;
    private static $engines;
    private $value;
    private $hex;
    private $precision;
    /**
     * @param mixed[] $modexps
     */
    public static function setEngine($main, $modexps = ['DefaultEngine'])
    {
        self::$engines = [];
        $fqmain = 'Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\\' . $main;
        if (!class_exists($fqmain) || !method_exists($fqmain, 'isValidEngine')) {
            throw new InvalidArgumentException("{$main} is not a valid engine");
        }
        if (!$fqmain::isValidEngine()) {
            throw new BadConfigurationException("{$main} is not setup correctly on this system");
        }
        self::$mainEngine = $fqmain;
        $found = \false;
        foreach ($modexps as $modexp) {
            try {
                $fqmain::setModExpEngine($modexp);
                $found = \true;
                break;
            } catch (Exception $e) {
            }
        }
        if (!$found) {
            throw new BadConfigurationException("No valid modular exponentiation engine found for {$main}");
        }
        self::$engines = [$main, $modexp];
    }
    public static function getEngine()
    {
        self::initialize_static_variables();
        return self::$engines;
    }
    private static function initialize_static_variables()
    {
        if (!isset(self::$mainEngine)) {
            $engines = [['GMP', ['DefaultEngine']], ['PHP64', ['OpenSSL']], ['BCMath', ['OpenSSL']], ['PHP32', ['OpenSSL']], ['PHP64', ['DefaultEngine']], ['PHP32', ['DefaultEngine']]];
            foreach ($engines as $engine) {
                try {
                    self::setEngine($engine[0], $engine[1]);
                    return;
                } catch (Exception $e) {
                }
            }
            throw new UnexpectedValueException('No valid BigInteger found. This is only possible when JIT is enabled on Windows and neither the GMP or BCMath extensions are available so either disable JIT or install GMP / BCMath');
        }
    }
    public function __construct($x = 0, $base = 10)
    {
        self::initialize_static_variables();
        if ($x instanceof self::$mainEngine) {
            $this->value = clone $x;
        } elseif ($x instanceof Engine) {
            $this->value = new static("{$x}");
            $this->value->setPrecision($x->getPrecision());
        } else {
            $this->value = new self::$mainEngine($x, $base);
        }
    }
    public function toString()
    {
        return $this->value->toString();
    }
    public function __toString()
    {
        return (string) $this->value;
    }
    public function __debugInfo()
    {
        return $this->value->__debugInfo();
    }
    public function toBytes($twos_compliment = \false)
    {
        return $this->value->toBytes($twos_compliment);
    }
    public function toHex($twos_compliment = \false)
    {
        return $this->value->toHex($twos_compliment);
    }
    public function toBits($twos_compliment = \false)
    {
        return $this->value->toBits($twos_compliment);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $y
     */
    public function add($y)
    {
        return new static($this->value->add($y->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $y
     */
    public function subtract($y)
    {
        return new static($this->value->subtract($y->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $x
     */
    public function multiply($x)
    {
        return new static($this->value->multiply($x->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $y
     */
    public function divide($y)
    {
        list($q, $r) = $this->value->divide($y->value);
        return [new static($q), new static($r)];
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function modInverse($n)
    {
        return new static($this->value->modInverse($n->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function extendedGCD($n)
    {
        extract($this->value->extendedGCD($n->value));
        return ['gcd' => new static($gcd), 'x' => new static($x), 'y' => new static($y)];
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function gcd($n)
    {
        return new static($this->value->gcd($n->value));
    }
    public function abs()
    {
        return new static($this->value->abs());
    }
    public function setPrecision($bits)
    {
        $this->value->setPrecision($bits);
    }
    public function getPrecision()
    {
        return $this->value->getPrecision();
    }
    public function __sleep()
    {
        $this->hex = $this->toHex(\true);
        $vars = ['hex'];
        if ($this->getPrecision() > 0) {
            $vars[] = 'precision';
        }
        return $vars;
    }
    public function __wakeup()
    {
        $temp = new static($this->hex, -16);
        $this->value = $temp->value;
        if ($this->precision > 0) {
            $this->setPrecision($this->precision);
        }
    }
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = ['hex' => $this->toHex(\true)];
        if ($this->precision > 0) {
            $result['precision'] = $this->getPrecision();
        }
        return $result;
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function powMod($e, $n)
    {
        return new static($this->value->powMod($e->value, $n->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $e
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function modPow($e, $n)
    {
        return new static($this->value->modPow($e->value, $n->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $y
     */
    public function compare($y)
    {
        return $this->value->compare($y->value);
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $x
     */
    public function equals($x)
    {
        return $this->value->equals($x->value);
    }
    public function bitwise_not()
    {
        return new static($this->value->bitwise_not());
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $x
     */
    public function bitwise_and($x)
    {
        return new static($this->value->bitwise_and($x->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $x
     */
    public function bitwise_or($x)
    {
        return new static($this->value->bitwise_or($x->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $x
     */
    public function bitwise_xor($x)
    {
        return new static($this->value->bitwise_xor($x->value));
    }
    public function bitwise_rightShift($shift)
    {
        return new static($this->value->bitwise_rightShift($shift));
    }
    public function bitwise_leftShift($shift)
    {
        return new static($this->value->bitwise_leftShift($shift));
    }
    public function bitwise_leftRotate($shift)
    {
        return new static($this->value->bitwise_leftRotate($shift));
    }
    public function bitwise_rightRotate($shift)
    {
        return new static($this->value->bitwise_rightRotate($shift));
    }
    public static function minMaxBits($bits)
    {
        self::initialize_static_variables();
        $class = self::$mainEngine;
        extract($class::minMaxBits($bits));
        return ['min' => new static($min), 'max' => new static($max)];
    }
    public function getLength()
    {
        return $this->value->getLength();
    }
    public function getLengthInBytes()
    {
        return $this->value->getLengthInBytes();
    }
    public static function random($size)
    {
        self::initialize_static_variables();
        $class = self::$mainEngine;
        return new static($class::random($size));
    }
    public static function randomPrime($size)
    {
        self::initialize_static_variables();
        $class = self::$mainEngine;
        return new static($class::randomPrime($size));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $max
     */
    public static function randomRangePrime($min, $max)
    {
        $class = self::$mainEngine;
        return new static($class::randomRangePrime($min->value, $max->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $max
     */
    public static function randomRange($min, $max)
    {
        $class = self::$mainEngine;
        return new static($class::randomRange($min->value, $max->value));
    }
    public function isPrime($t = \false)
    {
        return $this->value->isPrime($t);
    }
    public function root($n = 2)
    {
        return new static($this->value->root($n));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $n
     */
    public function pow($n)
    {
        return new static($this->value->pow($n->value));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger ...$nums
     */
    public static function min(...$nums)
    {
        $class = self::$mainEngine;
        $nums = array_map(function ($num) {
            return $num->value;
        }, $nums);
        return new static($class::min(...$nums));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger ...$nums
     */
    public static function max(...$nums)
    {
        $class = self::$mainEngine;
        $nums = array_map(function ($num) {
            return $num->value;
        }, $nums);
        return new static($class::max(...$nums));
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $min
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $max
     */
    public function between($min, $max)
    {
        return $this->value->between($min->value, $max->value);
    }
    public function __clone()
    {
        $this->value = clone $this->value;
    }
    public function isOdd()
    {
        return $this->value->isOdd();
    }
    public function testBit($x)
    {
        return $this->value->testBit($x);
    }
    public function isNegative()
    {
        return $this->value->isNegative();
    }
    public function negate()
    {
        return new static($this->value->negate());
    }
    /**
     * @param \Staatic\Vendor\phpseclib3\Math\BigInteger $r
     */
    public static function scan1divide($r)
    {
        $class = self::$mainEngine;
        return $class::scan1divide($r->value);
    }
    public function createRecurringModuloFunction()
    {
        $func = $this->value->createRecurringModuloFunction();
        return function (BigInteger $x) use ($func) {
            return new static($func($x->value));
        };
    }
    public function bitwise_split($split)
    {
        return array_map(function ($val) {
            return new static($val);
        }, $this->value->bitwise_split($split));
    }
}
