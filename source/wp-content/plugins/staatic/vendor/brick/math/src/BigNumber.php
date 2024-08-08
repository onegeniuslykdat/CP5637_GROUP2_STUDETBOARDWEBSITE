<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math;

use JsonSerializable;
use InvalidArgumentException;
use Staatic\Vendor\Brick\Math\Exception\DivisionByZeroException;
use Staatic\Vendor\Brick\Math\Exception\MathException;
use Staatic\Vendor\Brick\Math\Exception\NumberFormatException;
use Staatic\Vendor\Brick\Math\Exception\RoundingNecessaryException;
abstract class BigNumber implements JsonSerializable
{
    private const PARSE_REGEXP_NUMERICAL = '/^' . '(?<sign>[\-\+])?' . '(?<integral>[0-9]+)?' . '(?<point>\.)?' . '(?<fractional>[0-9]+)?' . '(?:[eE](?<exponent>[\-\+]?[0-9]+))?' . '$/';
    private const PARSE_REGEXP_RATIONAL = '/^' . '(?<sign>[\-\+])?' . '(?<numerator>[0-9]+)' . '\/?' . '(?<denominator>[0-9]+)' . '$/';
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $value
     * @return static
     */
    final public static function of($value)
    {
        $value = self::_of($value);
        if (static::class === BigNumber::class) {
            assert($value instanceof static);
            return $value;
        }
        return static::from($value);
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $value
     */
    private static function _of($value): BigNumber
    {
        if ($value instanceof BigNumber) {
            return $value;
        }
        if (\is_int($value)) {
            return new BigInteger((string) $value);
        }
        if (is_float($value)) {
            $value = (string) $value;
        }
        if (strpos($value, '/') !== false) {
            if (\preg_match(self::PARSE_REGEXP_RATIONAL, $value, $matches) !== 1) {
                throw NumberFormatException::invalidFormat($value);
            }
            $sign = $matches['sign'];
            $numerator = $matches['numerator'];
            $denominator = $matches['denominator'];
            assert($numerator !== null);
            assert($denominator !== null);
            $numerator = self::cleanUp($sign, $numerator);
            $denominator = self::cleanUp(null, $denominator);
            if ($denominator === '0') {
                throw DivisionByZeroException::denominatorMustNotBeZero();
            }
            return new BigRational(new BigInteger($numerator), new BigInteger($denominator), \false);
        } else {
            if (\preg_match(self::PARSE_REGEXP_NUMERICAL, $value, $matches) !== 1) {
                throw NumberFormatException::invalidFormat($value);
            }
            array_walk_recursive($matches, function (&$value) {
                if ($value === '') {
                    $value = null;
                }
            });
            $sign = $matches['sign'];
            $point = $matches['point'];
            $integral = $matches['integral'];
            $fractional = $matches['fractional'];
            $exponent = $matches['exponent'];
            if ($integral === null && $fractional === null) {
                throw NumberFormatException::invalidFormat($value);
            }
            if ($integral === null) {
                $integral = '0';
            }
            if ($point !== null || $exponent !== null) {
                $fractional = $fractional ?? '';
                $exponent = ($exponent !== null) ? (int) $exponent : 0;
                if ($exponent === \PHP_INT_MIN || $exponent === \PHP_INT_MAX) {
                    throw new NumberFormatException('Exponent too large.');
                }
                $unscaledValue = self::cleanUp($sign, $integral . $fractional);
                $scale = \strlen($fractional) - $exponent;
                if ($scale < 0) {
                    if ($unscaledValue !== '0') {
                        $unscaledValue .= \str_repeat('0', -$scale);
                    }
                    $scale = 0;
                }
                return new BigDecimal($unscaledValue, $scale);
            }
            $integral = self::cleanUp($sign, $integral);
            return new BigInteger($integral);
        }
        array_walk_recursive($matches, function (&$value) {
            if ($value === '') {
                $value = null;
            }
        });
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber $number
     * @return static
     */
    abstract protected static function from($number);
    /**
     * @param string $value
     */
    final protected function newBigInteger($value): BigInteger
    {
        return new BigInteger($value);
    }
    /**
     * @param string $value
     * @param int $scale
     */
    final protected function newBigDecimal($value, $scale = 0): BigDecimal
    {
        return new BigDecimal($value, $scale);
    }
    /**
     * @param BigInteger $numerator
     * @param BigInteger $denominator
     * @param bool $checkDenominator
     */
    final protected function newBigRational($numerator, $denominator, $checkDenominator): BigRational
    {
        return new BigRational($numerator, $denominator, $checkDenominator);
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string ...$values
     * @return static
     */
    final public static function min(...$values)
    {
        $min = null;
        foreach ($values as $value) {
            $value = static::of($value);
            if ($min === null || $value->isLessThan($min)) {
                $min = $value;
            }
        }
        if ($min === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }
        return $min;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string ...$values
     * @return static
     */
    final public static function max(...$values)
    {
        $max = null;
        foreach ($values as $value) {
            $value = static::of($value);
            if ($max === null || $value->isGreaterThan($max)) {
                $max = $value;
            }
        }
        if ($max === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }
        return $max;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string ...$values
     * @return static
     */
    final public static function sum(...$values)
    {
        $sum = null;
        foreach ($values as $value) {
            $value = static::of($value);
            $sum = ($sum === null) ? $value : self::add($sum, $value);
        }
        if ($sum === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }
        return $sum;
    }
    private static function add(BigNumber $a, BigNumber $b): BigNumber
    {
        if ($a instanceof BigRational) {
            return $a->plus($b);
        }
        if ($b instanceof BigRational) {
            return $b->plus($a);
        }
        if ($a instanceof BigDecimal) {
            return $a->plus($b);
        }
        if ($b instanceof BigDecimal) {
            return $b->plus($a);
        }
        return $a->plus($b);
    }
    private static function cleanUp(?string $sign, string $number): string
    {
        $number = \ltrim($number, '0');
        if ($number === '') {
            return '0';
        }
        return ($sign === '-') ? '-' . $number : $number;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    final public function isEqualTo($that): bool
    {
        return $this->compareTo($that) === 0;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    final public function isLessThan($that): bool
    {
        return $this->compareTo($that) < 0;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    final public function isLessThanOrEqualTo($that): bool
    {
        return $this->compareTo($that) <= 0;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    final public function isGreaterThan($that): bool
    {
        return $this->compareTo($that) > 0;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    final public function isGreaterThanOrEqualTo($that): bool
    {
        return $this->compareTo($that) >= 0;
    }
    final public function isZero(): bool
    {
        return $this->getSign() === 0;
    }
    final public function isNegative(): bool
    {
        return $this->getSign() < 0;
    }
    final public function isNegativeOrZero(): bool
    {
        return $this->getSign() <= 0;
    }
    final public function isPositive(): bool
    {
        return $this->getSign() > 0;
    }
    final public function isPositiveOrZero(): bool
    {
        return $this->getSign() >= 0;
    }
    abstract public function getSign(): int;
    /**
     * @param \Staatic\Vendor\Brick\Math\BigNumber|int|float|string $that
     */
    abstract public function compareTo($that): int;
    abstract public function toBigInteger(): BigInteger;
    abstract public function toBigDecimal(): BigDecimal;
    abstract public function toBigRational(): BigRational;
    /**
     * @param int $scale
     * @param RoundingMode $roundingMode
     */
    abstract public function toScale($scale, $roundingMode = RoundingMode::UNNECESSARY): BigDecimal;
    abstract public function toInt(): int;
    abstract public function toFloat(): float;
    abstract public function __toString(): string;
    final public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
