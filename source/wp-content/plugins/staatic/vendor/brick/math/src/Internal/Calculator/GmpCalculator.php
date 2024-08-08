<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math\Internal\Calculator;

use Staatic\Vendor\Brick\Math\Internal\Calculator;
class GmpCalculator extends Calculator
{
    /**
     * @param string $a
     * @param string $b
     */
    public function add($a, $b): string
    {
        return \gmp_strval(\gmp_add($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function sub($a, $b): string
    {
        return \gmp_strval(\gmp_sub($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function mul($a, $b): string
    {
        return \gmp_strval(\gmp_mul($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divQ($a, $b): string
    {
        return \gmp_strval(\gmp_div_q($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divR($a, $b): string
    {
        return \gmp_strval(\gmp_div_r($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function divQR($a, $b): array
    {
        [$q, $r] = \gmp_div_qr($a, $b);
        return [\gmp_strval($q), \gmp_strval($r)];
    }
    /**
     * @param string $a
     * @param int $e
     */
    public function pow($a, $e): string
    {
        return \gmp_strval(\gmp_pow($a, $e));
    }
    /**
     * @param string $x
     * @param string $m
     */
    public function modInverse($x, $m): ?string
    {
        $result = \gmp_invert($x, $m);
        if ($result === \false) {
            return null;
        }
        return \gmp_strval($result);
    }
    /**
     * @param string $base
     * @param string $exp
     * @param string $mod
     */
    public function modPow($base, $exp, $mod): string
    {
        return \gmp_strval(\gmp_powm($base, $exp, $mod));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function gcd($a, $b): string
    {
        return \gmp_strval(\gmp_gcd($a, $b));
    }
    /**
     * @param string $number
     * @param int $base
     */
    public function fromBase($number, $base): string
    {
        return \gmp_strval(\gmp_init($number, $base));
    }
    /**
     * @param string $number
     * @param int $base
     */
    public function toBase($number, $base): string
    {
        return \gmp_strval($number, $base);
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function and($a, $b): string
    {
        return \gmp_strval(\gmp_and($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function or($a, $b): string
    {
        return \gmp_strval(\gmp_or($a, $b));
    }
    /**
     * @param string $a
     * @param string $b
     */
    public function xor($a, $b): string
    {
        return \gmp_strval(\gmp_xor($a, $b));
    }
    /**
     * @param string $n
     */
    public function sqrt($n): string
    {
        return \gmp_strval(\gmp_sqrt($n));
    }
}
