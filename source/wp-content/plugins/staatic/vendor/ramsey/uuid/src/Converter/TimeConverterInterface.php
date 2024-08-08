<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter;

use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
interface TimeConverterInterface
{
    /**
     * @param string $seconds
     * @param string $microseconds
     */
    public function calculateTime($seconds, $microseconds): Hexadecimal;
    /**
     * @param Hexadecimal $uuidTimestamp
     */
    public function convertTime($uuidTimestamp): Time;
}
