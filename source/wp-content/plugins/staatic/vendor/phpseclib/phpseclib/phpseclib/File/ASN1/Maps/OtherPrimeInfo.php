<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class OtherPrimeInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['prime' => ['type' => ASN1::TYPE_INTEGER], 'exponent' => ['type' => ASN1::TYPE_INTEGER], 'coefficient' => ['type' => ASN1::TYPE_INTEGER]]];
}
