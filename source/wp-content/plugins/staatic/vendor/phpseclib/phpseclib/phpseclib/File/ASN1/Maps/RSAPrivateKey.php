<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class RSAPrivateKey
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => ASN1::TYPE_INTEGER, 'mapping' => ['two-prime', 'multi']], 'modulus' => ['type' => ASN1::TYPE_INTEGER], 'publicExponent' => ['type' => ASN1::TYPE_INTEGER], 'privateExponent' => ['type' => ASN1::TYPE_INTEGER], 'prime1' => ['type' => ASN1::TYPE_INTEGER], 'prime2' => ['type' => ASN1::TYPE_INTEGER], 'exponent1' => ['type' => ASN1::TYPE_INTEGER], 'exponent2' => ['type' => ASN1::TYPE_INTEGER], 'coefficient' => ['type' => ASN1::TYPE_INTEGER], 'otherPrimeInfos' => OtherPrimeInfos::MAP + ['optional' => \true]]];
}
