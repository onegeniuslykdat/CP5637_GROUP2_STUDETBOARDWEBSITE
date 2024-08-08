<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class secp112r2 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('DB7C2ABF62E35E668076BEAD208B', 16));
        $this->setCoefficients(new BigInteger('6127C24C05F38A0AAAF65C0EF02C', 16), new BigInteger('51DEF1815DB5ED74FCC34C85D709', 16));
        $this->setBasePoint(new BigInteger('4BA30AB5E892B4E1649DD0928643', 16), new BigInteger('ADCD46F5882E3747DEF36E956E97', 16));
        $this->setOrder(new BigInteger('36DF0AAFD8B8D7597CA10520D04B', 16));
    }
}
