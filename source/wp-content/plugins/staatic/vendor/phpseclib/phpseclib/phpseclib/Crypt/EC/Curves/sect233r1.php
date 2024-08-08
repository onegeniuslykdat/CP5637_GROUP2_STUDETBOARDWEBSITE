<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect233r1 extends Binary
{
    public function __construct()
    {
        $this->setModulo(233, 74, 0);
        $this->setCoefficients('000000000000000000000000000000000000000000000000000000000001', '0066647EDE6C332C7F8C0923BB58213B333B20E9CE4281FE115F7D8F90AD');
        $this->setBasePoint('00FAC9DFCBAC8313BB2139F1BB755FEF65BC391F8B36F8F8EB7371FD558B', '01006A08A41903350678E58528BEBF8A0BEFF867A7CA36716F7E01F81052');
        $this->setOrder(new BigInteger('01000000000000000000000000000013E974E72F8A6922031D2603CFE0D7', 16));
    }
}
