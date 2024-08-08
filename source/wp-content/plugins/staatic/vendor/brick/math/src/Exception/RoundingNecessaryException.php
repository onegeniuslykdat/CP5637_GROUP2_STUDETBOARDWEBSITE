<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math\Exception;

class RoundingNecessaryException extends MathException
{
    public static function roundingNecessary(): RoundingNecessaryException
    {
        return new self('Rounding is necessary to represent the result of the operation at this scale.');
    }
}
