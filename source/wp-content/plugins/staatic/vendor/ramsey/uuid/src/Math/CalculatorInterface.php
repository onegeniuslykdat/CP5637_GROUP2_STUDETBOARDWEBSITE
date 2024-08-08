<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Math;

use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Type\NumberInterface;
interface CalculatorInterface
{
    /**
     * @param NumberInterface $augend
     * @param NumberInterface ...$addends
     */
    public function add($augend, ...$addends): NumberInterface;
    /**
     * @param NumberInterface $minuend
     * @param NumberInterface ...$subtrahends
     */
    public function subtract($minuend, ...$subtrahends): NumberInterface;
    /**
     * @param NumberInterface $multiplicand
     * @param NumberInterface ...$multipliers
     */
    public function multiply($multiplicand, ...$multipliers): NumberInterface;
    /**
     * @param int $roundingMode
     * @param int $scale
     * @param NumberInterface $dividend
     * @param NumberInterface ...$divisors
     */
    public function divide($roundingMode, $scale, $dividend, ...$divisors): NumberInterface;
    /**
     * @param string $value
     * @param int $base
     */
    public function fromBase($value, $base);
    /**
     * @param IntegerObject $value
     * @param int $base
     */
    public function toBase($value, $base): string;
    /**
     * @param IntegerObject $value
     */
    public function toHexadecimal($value): Hexadecimal;
    /**
     * @param Hexadecimal $value
     */
    public function toInteger($value);
}
