<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Extension
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['extnId' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'critical' => ['type' => ASN1::TYPE_BOOLEAN, 'optional' => \true, 'default' => \false], 'extnValue' => ['type' => ASN1::TYPE_OCTET_STRING]]];
}
