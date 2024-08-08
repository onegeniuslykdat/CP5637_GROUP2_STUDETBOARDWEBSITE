<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class brainpoolP160r1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('E95E4A5F737059DC60DFC7AD95B3D8139515620F', 16));
        $this->setCoefficients(new BigInteger('340E7BE2A280EB74E2BE61BADA745D97E8F7C300', 16), new BigInteger('1E589A8595423412134FAA2DBDEC95C8D8675E58', 16));
        $this->setBasePoint(new BigInteger('BED5AF16EA3F6A4F62938C4631EB5AF7BDBCDBC3', 16), new BigInteger('1667CB477A1A8EC338F94741669C976316DA6321', 16));
        $this->setOrder(new BigInteger('E95E4A5F737059DC60DF5991D45029409E60FC09', 16));
    }
}
