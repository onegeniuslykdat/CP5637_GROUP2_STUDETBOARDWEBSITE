<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PrivateDomainName
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'children' => ['numeric' => ['type' => ASN1::TYPE_NUMERIC_STRING], 'printable' => ['type' => ASN1::TYPE_PRINTABLE_STRING]]];
}
