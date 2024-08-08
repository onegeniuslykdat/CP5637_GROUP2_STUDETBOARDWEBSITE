<?php

namespace Staatic\Vendor\phpseclib3\Math\BinaryField;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\BinaryField;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField\Integer as Base;
class Integer extends Base
{
    protected $value;
    protected $instanceID;
    protected static $modulo;
    protected static $reduce;
    public function __construct($instanceID, $num = '')
    {
        $this->instanceID = $instanceID;
        if (!strlen($num)) {
            $this->value = '';
        } else {
            $reduce = static::$reduce[$instanceID];
            $this->value = $reduce($num);
        }
    }
    public static function setModulo($instanceID, $modulo)
    {
        static::$modulo[$instanceID] = $modulo;
    }
    /**
     * @param callable $function
     */
    public static function setRecurringModuloFunction($instanceID, $function)
    {
        static::$reduce[$instanceID] = $function;
    }
    private static function checkInstance(self $x, self $y)
    {
        if ($x->instanceID != $y->instanceID) {
            throw new UnexpectedValueException('The instances of the two BinaryField\Integer objects do not match');
        }
    }
    /**
     * @param $this $x
     */
    public function equals($x)
    {
        static::checkInstance($this, $x);
        return $this->value == $x->value;
    }
    /**
     * @param $this $x
     */
    public function compare($x)
    {
        static::checkInstance($this, $x);
        $a = $this->value;
        $b = $x->value;
        $length = max(strlen($a), strlen($b));
        $a = str_pad($a, $length, "\x00", \STR_PAD_LEFT);
        $b = str_pad($b, $length, "\x00", \STR_PAD_LEFT);
        return strcmp($a, $b);
    }
    private static function deg($x)
    {
        $x = ltrim($x, "\x00");
        $xbit = decbin(ord($x[0]));
        $xlen = ($xbit == '0') ? 0 : strlen($xbit);
        $len = strlen($x);
        if (!$len) {
            return -1;
        }
        return 8 * strlen($x) - 9 + $xlen;
    }
    private static function polynomialDivide($x, $y)
    {
        $q = chr(0);
        $d = static::deg($y);
        $r = $x;
        while (($degr = static::deg($r)) >= $d) {
            $s = '1' . str_repeat('0', $degr - $d);
            $s = BinaryField::base2ToBase256($s);
            $length = max(strlen($s), strlen($q));
            $q = (!isset($q)) ? $s : (str_pad($q, $length, "\x00", \STR_PAD_LEFT) ^ str_pad($s, $length, "\x00", \STR_PAD_LEFT));
            $s = static::polynomialMultiply($s, $y);
            $length = max(strlen($r), strlen($s));
            $r = str_pad($r, $length, "\x00", \STR_PAD_LEFT) ^ str_pad($s, $length, "\x00", \STR_PAD_LEFT);
        }
        return [ltrim($q, "\x00"), ltrim($r, "\x00")];
    }
    private static function regularPolynomialMultiply($x, $y)
    {
        $precomputed = [ltrim($x, "\x00")];
        $x = strrev(BinaryField::base256ToBase2($x));
        $y = strrev(BinaryField::base256ToBase2($y));
        if (strlen($x) == strlen($y)) {
            $length = strlen($x);
        } else {
            $length = max(strlen($x), strlen($y));
            $x = str_pad($x, $length, '0');
            $y = str_pad($y, $length, '0');
        }
        $result = str_repeat('0', 2 * $length - 1);
        $result = BinaryField::base2ToBase256($result);
        $size = strlen($result);
        $x = strrev($x);
        for ($i = 1; $i < 8; $i++) {
            $precomputed[$i] = BinaryField::base2ToBase256($x . str_repeat('0', $i));
        }
        for ($i = 0; $i < strlen($y); $i++) {
            if ($y[$i] == '1') {
                $temp = $precomputed[$i & 7] . str_repeat("\x00", $i >> 3);
                $result ^= str_pad($temp, $size, "\x00", \STR_PAD_LEFT);
            }
        }
        return $result;
    }
    private static function polynomialMultiply($x, $y)
    {
        if (strlen($x) == strlen($y)) {
            $length = strlen($x);
        } else {
            $length = max(strlen($x), strlen($y));
            $x = str_pad($x, $length, "\x00", \STR_PAD_LEFT);
            $y = str_pad($y, $length, "\x00", \STR_PAD_LEFT);
        }
        switch (\true) {
            case \PHP_INT_SIZE == 8 && $length <= 4:
                return ($length != 4) ? self::subMultiply(str_pad($x, 4, "\x00", \STR_PAD_LEFT), str_pad($y, 4, "\x00", \STR_PAD_LEFT)) : self::subMultiply($x, $y);
            case \PHP_INT_SIZE == 4 || $length > 32:
                return self::regularPolynomialMultiply($x, $y);
        }
        $m = $length >> 1;
        $x1 = substr($x, 0, -$m);
        $x0 = substr($x, -$m);
        $y1 = substr($y, 0, -$m);
        $y0 = substr($y, -$m);
        $z2 = self::polynomialMultiply($x1, $y1);
        $z0 = self::polynomialMultiply($x0, $y0);
        $z1 = self::polynomialMultiply(self::subAdd2($x1, $x0), self::subAdd2($y1, $y0));
        $z1 = self::subAdd3($z1, $z2, $z0);
        $xy = self::subAdd3($z2 . str_repeat("\x00", 2 * $m), $z1 . str_repeat("\x00", $m), $z0);
        return ltrim($xy, "\x00");
    }
    private static function subMultiply($x, $y)
    {
        $x = unpack('N', $x)[1];
        $y = unpack('N', $y)[1];
        $x0 = $x & 0x11111111;
        $x1 = $x & 0x22222222;
        $x2 = $x & 0x44444444;
        $x3 = $x & 0x88888888;
        $y0 = $y & 0x11111111;
        $y1 = $y & 0x22222222;
        $y2 = $y & 0x44444444;
        $y3 = $y & 0x88888888;
        $z0 = $x0 * $y0 ^ $x1 * $y3 ^ $x2 * $y2 ^ $x3 * $y1;
        $z1 = $x0 * $y1 ^ $x1 * $y0 ^ $x2 * $y3 ^ $x3 * $y2;
        $z2 = $x0 * $y2 ^ $x1 * $y1 ^ $x2 * $y0 ^ $x3 * $y3;
        $z3 = $x0 * $y3 ^ $x1 * $y2 ^ $x2 * $y1 ^ $x3 * $y0;
        $z0 &= 0x1111111111111111;
        $z1 &= 0x2222222222222222;
        $z2 &= 0x4444444444444444;
        $z3 &= -8608480567731124088;
        $z = $z0 | $z1 | $z2 | $z3;
        return pack('J', $z);
    }
    private static function subAdd2($x, $y)
    {
        $length = max(strlen($x), strlen($y));
        $x = str_pad($x, $length, "\x00", \STR_PAD_LEFT);
        $y = str_pad($y, $length, "\x00", \STR_PAD_LEFT);
        return $x ^ $y;
    }
    private static function subAdd3($x, $y, $z)
    {
        $length = max(strlen($x), strlen($y), strlen($z));
        $x = str_pad($x, $length, "\x00", \STR_PAD_LEFT);
        $y = str_pad($y, $length, "\x00", \STR_PAD_LEFT);
        $z = str_pad($z, $length, "\x00", \STR_PAD_LEFT);
        return $x ^ $y ^ $z;
    }
    /**
     * @param $this $y
     */
    public function add($y)
    {
        static::checkInstance($this, $y);
        $length = strlen(static::$modulo[$this->instanceID]);
        $x = str_pad($this->value, $length, "\x00", \STR_PAD_LEFT);
        $y = str_pad($y->value, $length, "\x00", \STR_PAD_LEFT);
        return new static($this->instanceID, $x ^ $y);
    }
    /**
     * @param $this $x
     */
    public function subtract($x)
    {
        return $this->add($x);
    }
    /**
     * @param $this $y
     */
    public function multiply($y)
    {
        static::checkInstance($this, $y);
        return new static($this->instanceID, static::polynomialMultiply($this->value, $y->value));
    }
    public function modInverse()
    {
        $remainder0 = static::$modulo[$this->instanceID];
        $remainder1 = $this->value;
        if ($remainder1 == '') {
            return new static($this->instanceID);
        }
        $aux0 = "\x00";
        $aux1 = "\x01";
        while ($remainder1 != "\x01") {
            list($q, $r) = static::polynomialDivide($remainder0, $remainder1);
            $remainder0 = $remainder1;
            $remainder1 = $r;
            $temp = static::polynomialMultiply($aux1, $q);
            $aux = str_pad($aux0, strlen($temp), "\x00", \STR_PAD_LEFT) ^ str_pad($temp, strlen($aux0), "\x00", \STR_PAD_LEFT);
            $aux0 = $aux1;
            $aux1 = $aux;
        }
        $temp = new static($this->instanceID);
        $temp->value = ltrim($aux1, "\x00");
        return $temp;
    }
    /**
     * @param $this $x
     */
    public function divide($x)
    {
        static::checkInstance($this, $x);
        $x = $x->modInverse();
        return $this->multiply($x);
    }
    public function negate()
    {
        $x = str_pad($this->value, strlen(static::$modulo[$this->instanceID]), "\x00", \STR_PAD_LEFT);
        return new static($this->instanceID, $x ^ static::$modulo[$this->instanceID]);
    }
    public static function getModulo($instanceID)
    {
        return static::$modulo[$instanceID];
    }
    public function toBytes()
    {
        return str_pad($this->value, strlen(static::$modulo[$this->instanceID]), "\x00", \STR_PAD_LEFT);
    }
    public function toHex()
    {
        return Strings::bin2hex($this->toBytes());
    }
    public function toBits()
    {
        return BinaryField::base256ToBase2($this->value);
    }
    public function toBigInteger()
    {
        return new BigInteger($this->value, 256);
    }
    public function __toString()
    {
        return (string) $this->toBigInteger();
    }
    public function __debugInfo()
    {
        return ['value' => $this->toHex()];
    }
}
