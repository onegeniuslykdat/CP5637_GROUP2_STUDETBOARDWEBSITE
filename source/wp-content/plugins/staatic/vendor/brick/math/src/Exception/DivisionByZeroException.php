<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math\Exception;

class DivisionByZeroException extends MathException
{
    public static function divisionByZero(): DivisionByZeroException
    {
        return new self('Division by zero.');
    }
    public static function modulusMustNotBeZero(): DivisionByZeroException
    {
        return new self('The modulus must not be zero.');
    }
    public static function denominatorMustNotBeZero(): DivisionByZeroException
    {
        return new self('The denominator of a rational number cannot be zero.');
    }
}
