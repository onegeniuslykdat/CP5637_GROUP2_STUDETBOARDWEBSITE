<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AnotherName
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['type-id' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'value' => ['type' => ASN1::TYPE_ANY, 'constant' => 0, 'optional' => \true, 'explicit' => \true]]];
}
