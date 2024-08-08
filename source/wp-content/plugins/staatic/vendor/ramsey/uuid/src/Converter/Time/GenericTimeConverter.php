<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter\Time;

use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\CalculatorInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\RoundingMode;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
use function explode;
use function str_pad;
use const STR_PAD_LEFT;
class GenericTimeConverter implements TimeConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;
    private const GREGORIAN_TO_UNIX_INTERVALS = '122192928000000000';
    private const SECOND_INTERVALS = '10000000';
    private const MICROSECOND_INTERVALS = '10';
    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
     * @param string $seconds
     * @param string $microseconds
     */
    public function calculateTime($seconds, $microseconds): Hexadecimal
    {
        $timestamp = new Time($seconds, $microseconds);
        $sec = $this->calculator->multiply($timestamp->getSeconds(), new IntegerObject(self::SECOND_INTERVALS));
        $usec = $this->calculator->multiply($timestamp->getMicroseconds(), new IntegerObject(self::MICROSECOND_INTERVALS));
        $uuidTime = $this->calculator->add($sec, $usec, new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS));
        $uuidTimeHex = str_pad($this->calculator->toHexadecimal($uuidTime)->toString(), 16, '0', STR_PAD_LEFT);
        return new Hexadecimal($uuidTimeHex);
    }
    /**
     * @param Hexadecimal $uuidTimestamp
     */
    public function convertTime($uuidTimestamp): Time
    {
        $epochNanoseconds = $this->calculator->subtract($this->calculator->toInteger($uuidTimestamp), new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS));
        $unixTimestamp = $this->calculator->divide(RoundingMode::HALF_UP, 6, $epochNanoseconds, new IntegerObject(self::SECOND_INTERVALS));
        $split = explode('.', (string) $unixTimestamp, 2);
        return new Time($split[0], $split[1] ?? 0);
    }
}
