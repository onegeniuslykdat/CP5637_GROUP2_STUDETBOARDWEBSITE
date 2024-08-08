<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

interface EncoderInterface
{
    /**
     * @param string $binString
     */
    public static function encode($binString): string;
    /**
     * @param string $encodedString
     * @param bool $strictPadding
     */
    public static function decode($encodedString, $strictPadding = \false): string;
}
