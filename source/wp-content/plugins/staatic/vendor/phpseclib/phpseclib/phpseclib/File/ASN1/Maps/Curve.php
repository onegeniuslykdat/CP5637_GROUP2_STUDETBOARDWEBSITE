<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Curve
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['a' => FieldElement::MAP, 'b' => FieldElement::MAP, 'seed' => ['type' => ASN1::TYPE_BIT_STRING, 'optional' => \true]]];
}
