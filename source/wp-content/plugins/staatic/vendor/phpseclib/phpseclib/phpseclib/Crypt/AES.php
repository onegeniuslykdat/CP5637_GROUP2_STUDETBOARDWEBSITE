<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use BadMethodCallException;
use LengthException;
class AES extends Rijndael
{
    public function setBlockLength($length)
    {
        throw new BadMethodCallException('The block length cannot be set for AES.');
    }
    public function setKeyLength($length)
    {
        switch ($length) {
            case 128:
            case 192:
            case 256:
                break;
            default:
                throw new LengthException('Key of size ' . $length . ' not supported by this algorithm. Only keys of sizes 128, 192 or 256 supported');
        }
        parent::setKeyLength($length);
    }
    public function setKey($key)
    {
        switch (strlen($key)) {
            case 16:
            case 24:
            case 32:
                break;
            default:
                throw new LengthException('Key of size ' . strlen($key) . ' not supported by this algorithm. Only keys of sizes 16, 24 or 32 supported');
        }
        parent::setKey($key);
    }
}
