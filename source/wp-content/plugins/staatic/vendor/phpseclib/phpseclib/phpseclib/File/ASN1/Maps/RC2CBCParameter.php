<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class RC2CBCParameter
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['rc2ParametersVersion' => ['type' => ASN1::TYPE_INTEGER, 'optional' => \true], 'iv' => ['type' => ASN1::TYPE_OCTET_STRING]]];
}
