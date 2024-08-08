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
class UnixTimeConverter implements TimeConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;
    private const MILLISECONDS = 1000;
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
        $sec = $this->calculator->multiply($timestamp->getSeconds(), new IntegerObject(self::MILLISECONDS));
        $usec = $this->calculator->divide(RoundingMode::DOWN, 0, $timestamp->getMicroseconds(), new IntegerObject(self::MILLISECONDS));
        $unixTime = $this->calculator->add($sec, $usec);
        $unixTimeHex = str_pad($this->calculator->toHexadecimal($unixTime)->toString(), 12, '0', STR_PAD_LEFT);
        return new Hexadecimal($unixTimeHex);
    }
    /**
     * @param Hexadecimal $uuidTimestamp
     */
    public function convertTime($uuidTimestamp): Time
    {
        $milliseconds = $this->calculator->toInteger($uuidTimestamp);
        $unixTimestamp = $this->calculator->divide(RoundingMode::HALF_UP, 6, $milliseconds, new IntegerObject(self::MILLISECONDS));
        $split = explode('.', (string) $unixTimestamp, 2);
        return new Time($split[0], $split[1] ?? '0');
    }
}
