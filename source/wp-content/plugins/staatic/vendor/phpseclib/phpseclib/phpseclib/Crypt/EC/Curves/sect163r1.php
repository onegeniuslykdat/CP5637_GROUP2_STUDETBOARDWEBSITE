<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect163r1 extends Binary
{
    public function __construct()
    {
        $this->setModulo(163, 7, 6, 3, 0);
        $this->setCoefficients('07B6882CAAEFA84F9554FF8428BD88E246D2782AE2', '0713612DCDDCB40AAB946BDA29CA91F73AF958AFD9');
        $this->setBasePoint('0369979697AB43897789566789567F787A7876A654', '00435EDB42EFAFB2989D51FEFCE3C80988F41FF883');
        $this->setOrder(new BigInteger('03FFFFFFFFFFFFFFFFFFFF48AAB689C29CA710279B', 16));
    }
}
