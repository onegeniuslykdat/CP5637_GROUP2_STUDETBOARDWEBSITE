<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter\Time;

use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
class BigNumberTimeConverter implements TimeConverterInterface
{
    /**
     * @var TimeConverterInterface
     */
    private $converter;
    public function __construct()
    {
        $this->converter = new GenericTimeConverter(new BrickMathCalculator());
    }
    /**
     * @param string $seconds
     * @param string $microseconds
     */
    public function calculateTime($seconds, $microseconds): Hexadecimal
    {
        return $this->converter->calculateTime($seconds, $microseconds);
    }
    /**
     * @param Hexadecimal $uuidTimestamp
     */
    public function convertTime($uuidTimestamp): Time
    {
        return $this->converter->convertTime($uuidTimestamp);
    }
}
