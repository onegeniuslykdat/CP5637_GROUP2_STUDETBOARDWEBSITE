<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class ORAddress
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['built-in-standard-attributes' => BuiltInStandardAttributes::MAP, 'built-in-domain-defined-attributes' => ['optional' => \true] + BuiltInDomainDefinedAttributes::MAP, 'extension-attributes' => ['optional' => \true] + ExtensionAttributes::MAP]];
}
