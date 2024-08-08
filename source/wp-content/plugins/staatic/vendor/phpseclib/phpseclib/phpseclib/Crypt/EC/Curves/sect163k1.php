<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect163k1 extends Binary
{
    public function __construct()
    {
        $this->setModulo(163, 7, 6, 3, 0);
        $this->setCoefficients('000000000000000000000000000000000000000001', '000000000000000000000000000000000000000001');
        $this->setBasePoint('02FE13C0537BBC11ACAA07D793DE4E6D5E5C94EEE8', '0289070FB05D38FF58321F2E800536D538CCDAA3D9');
        $this->setOrder(new BigInteger('04000000000000000000020108A2E0CC0D99F8A5EF', 16));
    }
}
