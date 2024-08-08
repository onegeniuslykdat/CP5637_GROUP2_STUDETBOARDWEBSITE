<?php

namespace Staatic\Vendor\phpseclib3\Math;

use OutOfBoundsException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Math\BinaryField\Integer;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField;
class BinaryField extends FiniteField
{
    private static $instanceCounter = 0;
    protected $instanceID;
    private $randomMax;
    public function __construct(...$indices)
    {
        $m = array_shift($indices);
        if ($m > 571) {
            throw new OutOfBoundsException('Degrees larger than 571 are not supported');
        }
        $val = str_repeat('0', $m) . '1';
        foreach ($indices as $index) {
            $val[$index] = '1';
        }
        $modulo = static::base2ToBase256(strrev($val));
        $mStart = 2 * $m - 2;
        $t = ceil($m / 8);
        $finalMask = chr((1 << $m % 8) - 1);
        if ($finalMask == "\x00") {
            $finalMask = "\xff";
        }
        $bitLen = $mStart + 1;
        $pad = ceil($bitLen / 8);
        $h = $bitLen & 7;
        $h = $h ? 8 - $h : 0;
        $r = rtrim(substr($val, 0, -1), '0');
        $u = [static::base2ToBase256(strrev($r))];
        for ($i = 1; $i < 8; $i++) {
            $u[] = static::base2ToBase256(strrev(str_repeat('0', $i) . $r));
        }
        $reduce = function ($c) use ($u, $mStart, $m, $t, $finalMask, $pad, $h) {
            $c = str_pad($c, $pad, "\x00", \STR_PAD_LEFT);
            for ($i = $mStart; $i >= $m;) {
                $g = $h >> 3;
                $mask = $h & 7;
                $mask = $mask ? 1 << 7 - $mask : 0x80;
                for (; $mask > 0; $mask >>= 1, $i--, $h++) {
                    if (ord($c[$g]) & $mask) {
                        $temp = $i - $m;
                        $j = $temp >> 3;
                        $k = $temp & 7;
                        $t1 = $j ? substr($c, 0, -$j) : $c;
                        $length = strlen($t1);
                        if ($length) {
                            $t2 = str_pad($u[$k], $length, "\x00", \STR_PAD_LEFT);
                            $temp = $t1 ^ $t2;
                            $c = $j ? substr_replace($c, $temp, 0, $length) : $temp;
                        }
                    }
                }
            }
            $c = substr($c, -$t);
            if (strlen($c) == $t) {
                $c[0] = $c[0] & $finalMask;
            }
            return ltrim($c, "\x00");
        };
        $this->instanceID = self::$instanceCounter++;
        Integer::setModulo($this->instanceID, $modulo);
        Integer::setRecurringModuloFunction($this->instanceID, $reduce);
        $this->randomMax = new BigInteger($modulo, 2);
    }
    public function newInteger($num)
    {
        return new Integer($this->instanceID, ($num instanceof BigInteger) ? $num->toBytes() : $num);
    }
    public function randomInteger()
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }
        return new Integer($this->instanceID, BigInteger::randomRange($one, $this->randomMax)->toBytes());
    }
    public function getLengthInBytes()
    {
        return strlen(Integer::getModulo($this->instanceID));
    }
    public function getLength()
    {
        return strlen(Integer::getModulo($this->instanceID)) << 3;
    }
    public static function base2ToBase256($x, $size = null)
    {
        $str = Strings::bits2bin($x);
        $pad = strlen($x) >> 3;
        if (strlen($x) & 3) {
            $pad++;
        }
        $str = str_pad($str, $pad, "\x00", \STR_PAD_LEFT);
        if (isset($size)) {
            $str = str_pad($str, $size, "\x00", \STR_PAD_LEFT);
        }
        return $str;
    }
    public static function base256ToBase2($x)
    {
        if (function_exists('gmp_import')) {
            return gmp_strval(gmp_import($x), 2);
        }
        return Strings::bin2bits($x);
    }
}
