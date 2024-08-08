<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DHParameter
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['prime' => ['type' => ASN1::TYPE_INTEGER], 'base' => ['type' => ASN1::TYPE_INTEGER], 'privateValueLength' => ['type' => ASN1::TYPE_INTEGER, 'optional' => \true]]];
}
