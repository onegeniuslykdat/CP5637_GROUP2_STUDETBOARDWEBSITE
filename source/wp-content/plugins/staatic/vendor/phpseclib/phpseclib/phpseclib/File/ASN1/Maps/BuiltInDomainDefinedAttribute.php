<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class BuiltInDomainDefinedAttribute
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['type' => ['type' => ASN1::TYPE_PRINTABLE_STRING], 'value' => ['type' => ASN1::TYPE_PRINTABLE_STRING]]];
}
