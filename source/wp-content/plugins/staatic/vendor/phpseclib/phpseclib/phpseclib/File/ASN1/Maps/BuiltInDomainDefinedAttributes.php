<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class BuiltInDomainDefinedAttributes
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'min' => 1, 'max' => 4, 'children' => BuiltInDomainDefinedAttribute::MAP];
}
