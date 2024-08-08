<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Math;

use Staatic\Vendor\Brick\Math\BigDecimal;
use Staatic\Vendor\Brick\Math\BigInteger;
use Staatic\Vendor\Brick\Math\Exception\MathException;
use Staatic\Vendor\Brick\Math\RoundingMode as BrickMathRounding;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Type\Decimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Type\NumberInterface;
final class BrickMathCalculator implements CalculatorInterface
{
    private const ROUNDING_MODE_MAP = [RoundingMode::UNNECESSARY => BrickMathRounding::UNNECESSARY, RoundingMode::UP => BrickMathRounding::UP, RoundingMode::DOWN => BrickMathRounding::DOWN, RoundingMode::CEILING => BrickMathRounding::CEILING, RoundingMode::FLOOR => BrickMathRounding::FLOOR, RoundingMode::HALF_UP => BrickMathRounding::HALF_UP, RoundingMode::HALF_DOWN => BrickMathRounding::HALF_DOWN, RoundingMode::HALF_CEILING => BrickMathRounding::HALF_CEILING, RoundingMode::HALF_FLOOR => BrickMathRounding::HALF_FLOOR, RoundingMode::HALF_EVEN => BrickMathRounding::HALF_EVEN];
    /**
     * @param NumberInterface $augend
     * @param NumberInterface ...$addends
     */
    public function add($augend, ...$addends): NumberInterface
    {
        $sum = BigInteger::of($augend->toString());
        foreach ($addends as $addend) {
            $sum = $sum->plus($addend->toString());
        }
        return new IntegerObject((string) $sum);
    }
    /**
     * @param NumberInterface $minuend
     * @param NumberInterface ...$subtrahends
     */
    public function subtract($minuend, ...$subtrahends): NumberInterface
    {
        $difference = BigInteger::of($minuend->toString());
        foreach ($subtrahends as $subtrahend) {
            $difference = $difference->minus($subtrahend->toString());
        }
        return new IntegerObject((string) $difference);
    }
    /**
     * @param NumberInterface $multiplicand
     * @param NumberInterface ...$multipliers
     */
    public function multiply($multiplicand, ...$multipliers): NumberInterface
    {
        $product = BigInteger::of($multiplicand->toString());
        foreach ($multipliers as $multiplier) {
            $product = $product->multipliedBy($multiplier->toString());
        }
        return new IntegerObject((string) $product);
    }
    /**
     * @param int $roundingMode
     * @param int $scale
     * @param NumberInterface $dividend
     * @param NumberInterface ...$divisors
     */
    public function divide($roundingMode, $scale, $dividend, ...$divisors): NumberInterface
    {
        $brickRounding = $this->getBrickRoundingMode($roundingMode);
        $quotient = BigDecimal::of($dividend->toString());
        foreach ($divisors as $divisor) {
            $quotient = $quotient->dividedBy($divisor->toString(), $scale, $brickRounding);
        }
        if ($scale === 0) {
            return new IntegerObject((string) $quotient->toBigInteger());
        }
        return new Decimal((string) $quotient);
    }
    /**
     * @param string $value
     * @param int $base
     */
    public function fromBase($value, $base)
    {
        try {
            return new IntegerObject((string) BigInteger::fromBase($value, $base));
        } catch (MathException|\InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
    /**
     * @param IntegerObject $value
     * @param int $base
     */
    public function toBase($value, $base): string
    {
        try {
            return BigInteger::of($value->toString())->toBase($base);
        } catch (MathException|\InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
    /**
     * @param IntegerObject $value
     */
    public function toHexadecimal($value): Hexadecimal
    {
        return new Hexadecimal($this->toBase($value, 16));
    }
    /**
     * @param Hexadecimal $value
     */
    public function toInteger($value)
    {
        return $this->fromBase($value->toString(), 16);
    }
    private function getBrickRoundingMode(int $roundingMode)
    {
        return self::ROUNDING_MODE_MAP[$roundingMode] ?? BrickMathRounding::UNNECESSARY;
    }
}
