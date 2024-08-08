<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Math;

final class RoundingMode
{
    private function __construct()
    {
    }
    public const UNNECESSARY = 0;
    public const UP = 1;
    public const DOWN = 2;
    public const CEILING = 3;
    public const FLOOR = 4;
    public const HALF_UP = 5;
    public const HALF_DOWN = 6;
    public const HALF_CEILING = 7;
    public const HALF_FLOOR = 8;
    public const HALF_EVEN = 9;
}
