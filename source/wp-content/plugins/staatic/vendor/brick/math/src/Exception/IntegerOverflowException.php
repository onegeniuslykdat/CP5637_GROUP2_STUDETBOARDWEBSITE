<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math\Exception;

use Staatic\Vendor\Brick\Math\BigInteger;
class IntegerOverflowException extends MathException
{
    /**
     * @param BigInteger $value
     */
    public static function toIntOverflow($value): IntegerOverflowException
    {
        $message = '%s is out of range %d to %d and cannot be represented as an integer.';
        return new self(\sprintf($message, (string) $value, \PHP_INT_MIN, \PHP_INT_MAX));
    }
}
