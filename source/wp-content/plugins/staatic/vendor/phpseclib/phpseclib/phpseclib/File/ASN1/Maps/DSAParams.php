<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DSAParams
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['p' => ['type' => ASN1::TYPE_INTEGER], 'q' => ['type' => ASN1::TYPE_INTEGER], 'g' => ['type' => ASN1::TYPE_INTEGER]]];
}
