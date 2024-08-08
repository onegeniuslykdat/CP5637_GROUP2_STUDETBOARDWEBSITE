<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class RSAPublicKey
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['modulus' => ['type' => ASN1::TYPE_INTEGER], 'publicExponent' => ['type' => ASN1::TYPE_INTEGER]]];
}
