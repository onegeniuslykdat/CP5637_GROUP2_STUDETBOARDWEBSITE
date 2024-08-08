<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class secp128r1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('FFFFFFFDFFFFFFFFFFFFFFFFFFFFFFFF', 16));
        $this->setCoefficients(new BigInteger('FFFFFFFDFFFFFFFFFFFFFFFFFFFFFFFC', 16), new BigInteger('E87579C11079F43DD824993C2CEE5ED3', 16));
        $this->setBasePoint(new BigInteger('161FF7528B899B2D0C28607CA52C5B86', 16), new BigInteger('CF5AC8395BAFEB13C02DA292DDED7A83', 16));
        $this->setOrder(new BigInteger('FFFFFFFE0000000075A30D1B9038A115', 16));
    }
}
