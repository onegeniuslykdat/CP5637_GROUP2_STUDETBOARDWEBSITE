<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class FieldID
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['fieldType' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'parameters' => ['type' => ASN1::TYPE_ANY, 'optional' => \true]]];
}
