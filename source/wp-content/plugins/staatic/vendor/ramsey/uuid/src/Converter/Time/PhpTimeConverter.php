<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter\Time;

use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Staatic\Vendor\Ramsey\Uuid\Math\CalculatorInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
use function count;
use function dechex;
use function explode;
use function is_float;
use function is_int;
use function str_pad;
use function strlen;
use function substr;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;
class PhpTimeConverter implements TimeConverterInterface
{
    private const GREGORIAN_TO_UNIX_INTERVALS = 0x1b21dd213814000;
    private const SECOND_INTERVALS = 10000000;
    private const MICROSECOND_INTERVALS = 10;
    /**
     * @var int
     */
    private $phpPrecision;
    /**
     * @var CalculatorInterface
     */
    private $calculator;
    /**
     * @var TimeConverterInterface
     */
    private $fallbackConverter;
    public function __construct(?CalculatorInterface $calculator = null, ?TimeConverterInterface $fallbackConverter = null)
    {
        if ($calculator === null) {
            $calculator = new BrickMathCalculator();
        }
        if ($fallbackConverter === null) {
            $fallbackConverter = new GenericTimeConverter($calculator);
        }
        $this->calculator = $calculator;
        $this->fallbackConverter = $fallbackConverter;
        $this->phpPrecision = (int) ini_get('precision');
    }
    /**
     * @param string $seconds
     * @param string $microseconds
     */
    public function calculateTime($seconds, $microseconds): Hexadecimal
    {
        $seconds = new IntegerObject($seconds);
        $microseconds = new IntegerObject($microseconds);
        $uuidTime = (int) $seconds->toString() * self::SECOND_INTERVALS + (int) $microseconds->toString() * self::MICROSECOND_INTERVALS + self::GREGORIAN_TO_UNIX_INTERVALS;
        if (!is_int($uuidTime)) {
            return $this->fallbackConverter->calculateTime($seconds->toString(), $microseconds->toString());
        }
        return new Hexadecimal(str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT));
    }
    /**
     * @param Hexadecimal $uuidTimestamp
     */
    public function convertTime($uuidTimestamp): Time
    {
        $timestamp = $this->calculator->toInteger($uuidTimestamp);
        $splitTime = $this->splitTime(((int) $timestamp->toString() - self::GREGORIAN_TO_UNIX_INTERVALS) / self::SECOND_INTERVALS);
        if (count($splitTime) === 0) {
            return $this->fallbackConverter->convertTime($uuidTimestamp);
        }
        return new Time($splitTime['sec'], $splitTime['usec']);
    }
    /**
     * @param float|int $time
     */
    private function splitTime($time): array
    {
        $split = explode('.', (string) $time, 2);
        if (is_float($time) && count($split) === 1) {
            return [];
        }
        if (count($split) === 1) {
            return ['sec' => $split[0], 'usec' => '0'];
        }
        if (strlen($split[1]) < 6 && strlen((string) $time) >= $this->phpPrecision) {
            return [];
        }
        $microseconds = $split[1];
        if (strlen($microseconds) > 6) {
            $roundingDigit = (int) substr($microseconds, 6, 1);
            $microseconds = (int) substr($microseconds, 0, 6);
            if ($roundingDigit >= 5) {
                $microseconds++;
            }
        }
        return ['sec' => $split[0], 'usec' => str_pad((string) $microseconds, 6, '0', STR_PAD_RIGHT)];
    }
}
