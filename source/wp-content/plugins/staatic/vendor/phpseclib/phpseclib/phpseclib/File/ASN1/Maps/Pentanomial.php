<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Pentanomial
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['k1' => ['type' => ASN1::TYPE_INTEGER], 'k2' => ['type' => ASN1::TYPE_INTEGER], 'k3' => ['type' => ASN1::TYPE_INTEGER]]];
}
