<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class ExtensionAttribute
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['extension-attribute-type' => ['type' => ASN1::TYPE_PRINTABLE_STRING, 'constant' => 0, 'optional' => \true, 'implicit' => \true], 'extension-attribute-value' => ['type' => ASN1::TYPE_ANY, 'constant' => 1, 'optional' => \true, 'explicit' => \true]]];
}
