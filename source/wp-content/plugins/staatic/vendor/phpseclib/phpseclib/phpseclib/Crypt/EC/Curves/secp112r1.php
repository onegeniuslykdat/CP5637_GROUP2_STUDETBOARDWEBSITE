<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class secp112r1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('DB7C2ABF62E35E668076BEAD208B', 16));
        $this->setCoefficients(new BigInteger('DB7C2ABF62E35E668076BEAD2088', 16), new BigInteger('659EF8BA043916EEDE8911702B22', 16));
        $this->setBasePoint(new BigInteger('09487239995A5EE76B55F9C2F098', 16), new BigInteger('A89CE5AF8724C0A23E0E0FF77500', 16));
        $this->setOrder(new BigInteger('DB7C2ABF62E35E7628DFAC6561C5', 16));
    }
}
