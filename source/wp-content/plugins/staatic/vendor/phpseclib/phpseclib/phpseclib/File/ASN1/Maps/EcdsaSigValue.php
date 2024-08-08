<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class EcdsaSigValue
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['r' => ['type' => ASN1::TYPE_INTEGER], 's' => ['type' => ASN1::TYPE_INTEGER]]];
}
