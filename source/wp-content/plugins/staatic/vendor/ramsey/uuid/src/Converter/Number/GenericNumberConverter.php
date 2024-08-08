<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter\Number;

use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\CalculatorInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
class GenericNumberConverter implements NumberConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;
    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
     * @param string $hex
     */
    public function fromHex($hex): string
    {
        return $this->calculator->fromBase($hex, 16)->toString();
    }
    /**
     * @param string $number
     */
    public function toHex($number): string
    {
        return $this->calculator->toBase(new IntegerObject($number), 16);
    }
}
