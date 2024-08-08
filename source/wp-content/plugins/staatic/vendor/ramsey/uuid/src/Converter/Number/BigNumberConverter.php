<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter\Number;

use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
class BigNumberConverter implements NumberConverterInterface
{
    /**
     * @var NumberConverterInterface
     */
    private $converter;
    public function __construct()
    {
        $this->converter = new GenericNumberConverter(new BrickMathCalculator());
    }
    /**
     * @param string $hex
     */
    public function fromHex($hex): string
    {
        return $this->converter->fromHex($hex);
    }
    /**
     * @param string $number
     */
    public function toHex($number): string
    {
        return $this->converter->toHex($number);
    }
}
