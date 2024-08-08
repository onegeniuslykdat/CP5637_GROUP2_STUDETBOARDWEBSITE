<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math\Internal\Calculator;

use Staatic\Vendor\Brick\Math\Internal\Calculator;
class BcMathCalculator extends Calculator
{
    /**
     * @param string $a
     * @param string $b
     */
    public function add($a, $b): string
    {
        return \bcadd($a, $b, 0);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function sub($a, $b): string
    {
        return \bcsub($a, $b, 0);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function mul($a, $b): string
    {
        return \bcmul($a, $b, 0);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divQ($a, $b): string
    {
        return \bcdiv($a, $b, 0);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divR($a, $b): string
    {
        return \bcmod($a, $b, 0);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divQR($a, $b): array
    {
        $q = \bcdiv($a, $b, 0);
        $r = \bcmod($a, $b, 0);
        return [$q, $r];
    }
    /**
     * @param string $a
     * @param int $e
     */
    public function pow($a, $e): string
    {
        return \bcpow($a, (string) $e, 0);
    }
    /**
     * @param string $base
     * @param string $exp
     * @param string $mod
     */
    public function modPow($base, $exp, $mod): string
    {
        return \bcpowmod($base, $exp, $mod, 0);
    }
    /**
     * @param string $n
     */
    public function sqrt($n): string
    {
        return \bcsqrt($n, 0);
    }
}
