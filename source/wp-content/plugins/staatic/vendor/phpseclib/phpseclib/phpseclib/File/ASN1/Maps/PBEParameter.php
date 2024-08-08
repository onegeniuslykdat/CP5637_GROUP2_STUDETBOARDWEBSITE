<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PBEParameter
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['salt' => ['type' => ASN1::TYPE_OCTET_STRING], 'iterationCount' => ['type' => ASN1::TYPE_INTEGER]]];
}
