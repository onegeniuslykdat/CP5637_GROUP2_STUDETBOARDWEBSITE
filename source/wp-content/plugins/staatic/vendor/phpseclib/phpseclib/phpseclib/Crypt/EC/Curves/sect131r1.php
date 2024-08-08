<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class sect131r1 extends Binary
{
    public function __construct()
    {
        $this->setModulo(131, 8, 3, 2, 0);
        $this->setCoefficients('07A11B09A76B562144418FF3FF8C2570B8', '0217C05610884B63B9C6C7291678F9D341');
        $this->setBasePoint('0081BAF91FDF9833C40F9C181343638399', '078C6E7EA38C001F73C8134B1B4EF9E150');
        $this->setOrder(new BigInteger('0400000000000000023123953A9464B54D', 16));
    }
}
