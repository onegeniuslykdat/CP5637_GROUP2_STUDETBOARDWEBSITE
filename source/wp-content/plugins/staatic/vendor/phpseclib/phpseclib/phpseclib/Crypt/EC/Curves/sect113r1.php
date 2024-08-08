<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect113r1 extends Binary
{
    public function __construct()
    {
        $this->setModulo(113, 9, 0);
        $this->setCoefficients('003088250CA6E7C7FE649CE85820F7', '00E8BEE4D3E2260744188BE0E9C723');
        $this->setBasePoint('009D73616F35F4AB1407D73562C10F', '00A52830277958EE84D1315ED31886');
        $this->setOrder(new BigInteger('0100000000000000D9CCEC8A39E56F', 16));
    }
}
