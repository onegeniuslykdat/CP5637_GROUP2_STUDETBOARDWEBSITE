<?php

namespace Staatic\Vendor\MyCLabs\Enum\PHPUnit;

use Staatic\Vendor\MyCLabs\Enum\Enum;
use Staatic\Vendor\SebastianBergmann\Comparator\ComparisonFailure;
final class Comparator extends \Staatic\Vendor\SebastianBergmann\Comparator\Comparator
{
    public function accepts($expected, $actual)
    {
        return $expected instanceof Enum && ($actual instanceof Enum || $actual === null);
    }
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = \false, $ignoreCase = \false)
    {
        if ($expected->equals($actual)) {
            return;
        }
        throw new ComparisonFailure($expected, $actual, $this->formatEnum($expected), $this->formatEnum($actual), \false, 'Failed asserting that two Enums are equal.');
    }
    private function formatEnum(Enum $enum = null)
    {
        if ($enum === null) {
            return "null";
        }
        return get_class($enum) . "::{$enum->getKey()}()";
    }
}
