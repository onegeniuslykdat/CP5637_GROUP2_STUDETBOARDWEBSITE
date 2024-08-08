<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use RangeException;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class Curve25519 extends Montgomery
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFED', 16));
        $this->a24 = $this->factory->newInteger(new BigInteger('121666'));
        $this->p = [$this->factory->newInteger(new BigInteger(9))];
        $this->setOrder(new BigInteger('1000000000000000000000000000000014DEF9DEA2F79CD65812631A5CF5D3ED', 16));
    }
    /**
     * @param mixed[] $p
     * @param BigInteger $d
     */
    public function multiplyPoint($p, $d)
    {
        $d = $d->toBytes();
        $d &= "\xf8" . str_repeat("\xff", 30) . "";
        $d = strrev($d);
        $d |= "@";
        $d = new BigInteger($d, -256);
        return parent::multiplyPoint($p, $d);
    }
    public function createRandomMultiplier()
    {
        return BigInteger::random(256);
    }
    /**
     * @param BigInteger $x
     */
    public function rangeCheck($x)
    {
        if ($x->getLength() > 256 || $x->isNegative()) {
            throw new RangeException('x must be a positive integer less than 256 bytes in length');
        }
    }
}
