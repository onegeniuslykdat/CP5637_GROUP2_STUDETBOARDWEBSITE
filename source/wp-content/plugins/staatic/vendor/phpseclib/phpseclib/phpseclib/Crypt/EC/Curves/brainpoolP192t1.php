<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class brainpoolP192t1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('C302F41D932A36CDA7A3463093D18DB78FCE476DE1A86297', 16));
        $this->setCoefficients(new BigInteger('C302F41D932A36CDA7A3463093D18DB78FCE476DE1A86294', 16), new BigInteger('13D56FFAEC78681E68F9DEB43B35BEC2FB68542E27897B79', 16));
        $this->setBasePoint(new BigInteger('3AE9E58C82F63C30282E1FE7BBF43FA72C446AF6F4618129', 16), new BigInteger('097E2C5667C2223A902AB5CA449D0084B7E5B3DE7CCC01C9', 16));
        $this->setOrder(new BigInteger('C302F41D932A36CDA7A3462F9E9E916B5BE8F1029AC4ACC1', 16));
    }
}
