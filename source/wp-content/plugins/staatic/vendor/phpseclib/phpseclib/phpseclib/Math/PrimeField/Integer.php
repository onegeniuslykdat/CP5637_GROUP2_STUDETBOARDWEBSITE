<?php

namespace Staatic\Vendor\phpseclib3\Math\PrimeField;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField\Integer as Base;
class Integer extends Base
{
    protected $value;
    protected $instanceID;
    protected static $modulo;
    protected static $reduce;
    protected static $zero;
    public function __construct($instanceID, BigInteger $num = null)
    {
        $this->instanceID = $instanceID;
        if (!isset($num)) {
            $this->value = clone static::$zero[static::class];
        } else {
            $reduce = static::$reduce[$instanceID];
            $this->value = $reduce($num);
        }
    }
    /**
     * @param BigInteger $modulo
     */
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
        if (!isset(static::$zero[static::class])) {
            static::$zero[static::class] = new BigInteger();
        }
    }
    public static function cleanupCache($instanceID)
    {
        unset(static::$modulo[$instanceID]);
        unset(static::$reduce[$instanceID]);
    }
    public static function getModulo($instanceID)
    {
        return static::$modulo[$instanceID];
    }
    /**
     * @param $this $x
     * @param $this $y
     */
    public static function checkInstance($x, $y)
    {
        if ($x->instanceID != $y->instanceID) {
            throw new UnexpectedValueException('The instances of the two PrimeField\Integer objects do not match');
        }
    }
    /**
     * @param $this $x
     */
    public function equals($x)
    {
        static::checkInstance($this, $x);
        return $this->value->equals($x->value);
    }
    /**
     * @param $this $x
     */
    public function compare($x)
    {
        static::checkInstance($this, $x);
        return $this->value->compare($x->value);
    }
    /**
     * @param $this $x
     */
    public function add($x)
    {
        static::checkInstance($this, $x);
        $temp = new static($this->instanceID);
        $temp->value = $this->value->add($x->value);
        if ($temp->value->compare(static::$modulo[$this->instanceID]) >= 0) {
            $temp->value = $temp->value->subtract(static::$modulo[$this->instanceID]);
        }
        return $temp;
    }
    /**
     * @param $this $x
     */
    public function subtract($x)
    {
        static::checkInstance($this, $x);
        $temp = new static($this->instanceID);
        $temp->value = $this->value->subtract($x->value);
        if ($temp->value->isNegative()) {
            $temp->value = $temp->value->add(static::$modulo[$this->instanceID]);
        }
        return $temp;
    }
    /**
     * @param $this $x
     */
    public function multiply($x)
    {
        static::checkInstance($this, $x);
        return new static($this->instanceID, $this->value->multiply($x->value));
    }
    /**
     * @param $this $x
     */
    public function divide($x)
    {
        static::checkInstance($this, $x);
        $denominator = $x->value->modInverse(static::$modulo[$this->instanceID]);
        return new static($this->instanceID, $this->value->multiply($denominator));
    }
    /**
     * @param BigInteger $x
     */
    public function pow($x)
    {
        $temp = new static($this->instanceID);
        $temp->value = $this->value->powMod($x, static::$modulo[$this->instanceID]);
        return $temp;
    }
    public function squareRoot()
    {
        static $one, $two;
        if (!isset($one)) {
            $one = new BigInteger(1);
            $two = new BigInteger(2);
        }
        $reduce = static::$reduce[$this->instanceID];
        $p_1 = static::$modulo[$this->instanceID]->subtract($one);
        $q = clone $p_1;
        $s = BigInteger::scan1divide($q);
        list($pow) = $p_1->divide($two);
        for ($z = $one; !$z->equals(static::$modulo[$this->instanceID]); $z = $z->add($one)) {
            $temp = $z->powMod($pow, static::$modulo[$this->instanceID]);
            if ($temp->equals($p_1)) {
                break;
            }
        }
        $m = new BigInteger($s);
        $c = $z->powMod($q, static::$modulo[$this->instanceID]);
        $t = $this->value->powMod($q, static::$modulo[$this->instanceID]);
        list($temp) = $q->add($one)->divide($two);
        $r = $this->value->powMod($temp, static::$modulo[$this->instanceID]);
        while (!$t->equals($one)) {
            for ($i = clone $one; $i->compare($m) < 0; $i = $i->add($one)) {
                if ($t->powMod($two->pow($i), static::$modulo[$this->instanceID])->equals($one)) {
                    break;
                }
            }
            if ($i->compare($m) == 0) {
                return \false;
            }
            $b = $c->powMod($two->pow($m->subtract($i)->subtract($one)), static::$modulo[$this->instanceID]);
            $m = $i;
            $c = $reduce($b->multiply($b));
            $t = $reduce($t->multiply($c));
            $r = $reduce($r->multiply($b));
        }
        return new static($this->instanceID, $r);
    }
    public function isOdd()
    {
        return $this->value->isOdd();
    }
    public function negate()
    {
        return new static($this->instanceID, static::$modulo[$this->instanceID]->subtract($this->value));
    }
    public function toBytes()
    {
        if (isset(static::$modulo[$this->instanceID])) {
            $length = static::$modulo[$this->instanceID]->getLengthInBytes();
            return str_pad($this->value->toBytes(), $length, "\x00", \STR_PAD_LEFT);
        }
        return $this->value->toBytes();
    }
    public function toHex()
    {
        return Strings::bin2hex($this->toBytes());
    }
    public function toBits()
    {
        static $length;
        if (!isset($length)) {
            $length = static::$modulo[$this->instanceID]->getLength();
        }
        return str_pad($this->value->toBits(), $length, '0', \STR_PAD_LEFT);
    }
    public function getNAF($w = 1)
    {
        $w++;
        $mask = new BigInteger((1 << $w) - 1);
        $sub = new BigInteger(1 << $w);
        $d = $this->toBigInteger();
        $d_i = [];
        $i = 0;
        while ($d->compare(static::$zero[static::class]) > 0) {
            if ($d->isOdd()) {
                $bigInteger = $d->testBit($w - 1) ? $d->bitwise_and($mask)->subtract($sub) : $d->bitwise_and($mask);
                $d = $d->subtract($bigInteger);
                $d_i[$i] = (int) $bigInteger->toString();
            } else {
                $d_i[$i] = 0;
            }
            $shift = (!$d->equals(static::$zero[static::class]) && $d->bitwise_and($mask)->equals(static::$zero[static::class])) ? $w : 1;
            $d = $d->bitwise_rightShift($shift);
            while (--$shift > 0) {
                $d_i[++$i] = 0;
            }
            $i++;
        }
        return $d_i;
    }
    public function toBigInteger()
    {
        return clone $this->value;
    }
    public function __toString()
    {
        return (string) $this->value;
    }
    public function __debugInfo()
    {
        return ['value' => $this->toHex()];
    }
}
