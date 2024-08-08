<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect131r2 extends Binary
{
    public function __construct()
    {
        $this->setModulo(131, 8, 3, 2, 0);
        $this->setCoefficients('03E5A88919D7CAFCBF415F07C2176573B2', '04B8266A46C55657AC734CE38F018F2192');
        $this->setBasePoint('0356DCD8F2F95031AD652D23951BB366A8', '0648F06D867940A5366D9E265DE9EB240F');
        $this->setOrder(new BigInteger('0400000000000000016954A233049BA98F', 16));
    }
}
