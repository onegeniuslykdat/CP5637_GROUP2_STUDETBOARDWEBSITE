<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class brainpoolP224r1 extends Prime
{
    public function __construct()
    {
        $this->setModulo(new BigInteger('D7C134AA264366862A18302575D1D787B09F075797DA89F57EC8C0FF', 16));
        $this->setCoefficients(new BigInteger('68A5E62CA9CE6C1C299803A6C1530B514E182AD8B0042A59CAD29F43', 16), new BigInteger('2580F63CCFE44138870713B1A92369E33E2135D266DBB372386C400B', 16));
        $this->setBasePoint(new BigInteger('0D9029AD2C7E5CF4340823B2A87DC68C9E4CE3174C1E6EFDEE12C07D', 16), new BigInteger('58AA56F772C0726F24C6B89E4ECDAC24354B9E99CAA3F6D3761402CD', 16));
        $this->setOrder(new BigInteger('D7C134AA264366862A18302575D0FB98D116BC4B6DDEBCA3A5A7939F', 16));
    }
}
