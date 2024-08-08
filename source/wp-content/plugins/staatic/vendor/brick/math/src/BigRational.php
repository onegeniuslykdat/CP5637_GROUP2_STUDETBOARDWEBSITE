<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math;

use LogicException;
use Staatic\Vendor\Brick\Math\Exception\DivisionByZeroException;
use Staatic\Vendor\Brick\Math\Exception\MathException;
use Staatic\Vendor\Brick\Math\Exception\NumberFormatException;
use Staatic\Vendor\Brick\Math\Exception\RoundingNecessaryException;
final class BigRational extends BigNumber
{
    /**
     * @readonly
     * @var BigInteger
     */
    private $numerator;
    /**
     * @readonly
     * @var BigInteger
     */
    private $denominator;
    protected function __construct(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator)
    {
        if ($checkDenominator) {
            if ($denominator->isZero()) {
                throw DivisionByZeroException::denominatorMustNotBeZero();
            }
            if ($denominator->isNegative()) {
                $numerator = $numerator->negated();
                $denominator = $denominator->negated();
            }
        }
        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }
    /**
     * @param BigNumber $number
     * @return static
     */
    protected static function from($number)
    {
        return $number->toBigRational();
    }
    /**
     * @param BigNumber|int|float|string $numerator
     * @param BigNumber|int|float|string $denominator
     */
    public static function nd($numerator, $denominator): BigRational
    {
        $numerator = BigInteger::of($numerator);
        $denominator = BigInteger::of($denominator);
        return new BigRational($numerator, $denominator, \true);
    }
    public static function zero(): BigRational
    {
        static $zero;
        if ($zero === null) {
            $zero = new BigRational(BigInteger::zero(), BigInteger::one(), \false);
        }
        return $zero;
    }
    public static function one(): BigRational
    {
        static $one;
        if ($one === null) {
            $one = new BigRational(BigInteger::one(), BigInteger::one(), \false);
        }
        return $one;
    }
    public static function ten(): BigRational
    {
        static $ten;
        if ($ten === null) {
            $ten = new BigRational(BigInteger::ten(), BigInteger::one(), \false);
        }
        return $ten;
    }
    public function getNumerator(): BigInteger
    {
        return $this->numerator;
    }
    public function getDenominator(): BigInteger
    {
        return $this->denominator;
    }
    public function quotient(): BigInteger
    {
        return $this->numerator->quotient($this->denominator);
    }
    public function remainder(): BigInteger
    {
        return $this->numerator->remainder($this->denominator);
    }
    public function quotientAndRemainder(): array
    {
        return $this->numerator->quotientAndRemainder($this->denominator);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function plus($that): BigRational
    {
        $that = BigRational::of($that);
        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->plus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);
        return new BigRational($numerator, $denominator, \false);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function minus($that): BigRational
    {
        $that = BigRational::of($that);
        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->minus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);
        return new BigRational($numerator, $denominator, \false);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function multipliedBy($that): BigRational
    {
        $that = BigRational::of($that);
        $numerator = $this->numerator->multipliedBy($that->numerator);
        $denominator = $this->denominator->multipliedBy($that->denominator);
        return new BigRational($numerator, $denominator, \false);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function dividedBy($that): BigRational
    {
        $that = BigRational::of($that);
        $numerator = $this->numerator->multipliedBy($that->denominator);
        $denominator = $this->denominator->multipliedBy($that->numerator);
        return new BigRational($numerator, $denominator, \true);
    }
    /**
     * @param int $exponent
     */
    public function power($exponent): BigRational
    {
        if ($exponent === 0) {
            $one = BigInteger::one();
            return new BigRational($one, $one, \false);
        }
        if ($exponent === 1) {
            return $this;
        }
        return new BigRational($this->numerator->power($exponent), $this->denominator->power($exponent), \false);
    }
    public function reciprocal(): BigRational
    {
        return new BigRational($this->denominator, $this->numerator, \true);
    }
    public function abs(): BigRational
    {
        return new BigRational($this->numerator->abs(), $this->denominator, \false);
    }
    public function negated(): BigRational
    {
        return new BigRational($this->numerator->negated(), $this->denominator, \false);
    }
    public function simplified(): BigRational
    {
        $gcd = $this->numerator->gcd($this->denominator);
        $numerator = $this->numerator->quotient($gcd);
        $denominator = $this->denominator->quotient($gcd);
        return new BigRational($numerator, $denominator, \false);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function compareTo($that): int
    {
        return $this->minus($that)->getSign();
    }
    public function getSign(): int
    {
        return $this->numerator->getSign();
    }
    public function toBigInteger(): BigInteger
    {
        $simplified = $this->simplified();
        if (!$simplified->denominator->isEqualTo(1)) {
            throw new RoundingNecessaryException('This rational number cannot be represented as an integer value without rounding.');
        }
        return $simplified->numerator;
    }
    public function toBigDecimal(): BigDecimal
    {
        return $this->numerator->toBigDecimal()->exactlyDividedBy($this->denominator);
    }
    public function toBigRational(): BigRational
    {
        return $this;
    }
    /**
     * @param int $scale
     * @param RoundingMode $roundingMode
     */
    public function toScale($scale, $roundingMode = RoundingMode::UNNECESSARY): BigDecimal
    {
        return $this->numerator->toBigDecimal()->dividedBy($this->denominator, $scale, $roundingMode);
    }
    public function toInt(): int
    {
        return $this->toBigInteger()->toInt();
    }
    public function toFloat(): float
    {
        $simplified = $this->simplified();
        return $simplified->numerator->toFloat() / $simplified->denominator->toFloat();
    }
    public function __toString(): string
    {
        $numerator = (string) $this->numerator;
        $denominator = (string) $this->denominator;
        if ($denominator === '1') {
            return $numerator;
        }
        return $this->numerator . '/' . $this->denominator;
    }
    public function __serialize(): array
    {
        return ['numerator' => $this->numerator, 'denominator' => $this->denominator];
    }
    public function __unserialize(array $data): void
    {
        if (isset($this->numerator)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }
        $this->numerator = $data['numerator'];
        $this->denominator = $data['denominator'];
    }
}
