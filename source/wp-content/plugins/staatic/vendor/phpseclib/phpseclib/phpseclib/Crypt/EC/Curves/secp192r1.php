<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class secp192r1 extends Prime
{
    public function __construct()
    {
        $modulo = new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFFFFFFFFFF', 16);
        $this->setModulo($modulo);
        $this->setCoefficients(new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFFFFFFFFFC', 16), new BigInteger('64210519E59C80E70FA7E9AB72243049FEB8DEECC146B9B1', 16));
        $this->setBasePoint(new BigInteger('188DA80EB03090F67CBF20EB43A18800F4FF0AFD82FF1012', 16), new BigInteger('07192B95FFC8DA78631011ED6B24CDD573F977A11E794811', 16));
        $this->setOrder(new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFF99DEF836146BC9B1B4D22831', 16));
    }
}
