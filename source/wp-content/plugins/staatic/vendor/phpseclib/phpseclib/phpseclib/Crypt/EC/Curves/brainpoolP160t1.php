<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class brainpoolP160t1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('E95E4A5F737059DC60DFC7AD95B3D8139515620F', 16));
        $this->setCoefficients(new BigInteger('E95E4A5F737059DC60DFC7AD95B3D8139515620C', 16), new BigInteger('7A556B6DAE535B7B51ED2C4D7DAA7A0B5C55F380', 16));
        $this->setBasePoint(new BigInteger('B199B13B9B34EFC1397E64BAEB05ACC265FF2378', 16), new BigInteger('ADD6718B7C7C1961F0991B842443772152C9E0AD', 16));
        $this->setOrder(new BigInteger('E95E4A5F737059DC60DF5991D45029409E60FC09', 16));
    }
}
