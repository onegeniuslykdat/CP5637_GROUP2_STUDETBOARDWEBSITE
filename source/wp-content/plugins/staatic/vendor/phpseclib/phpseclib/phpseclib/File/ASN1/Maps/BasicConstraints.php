<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class BasicConstraints
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['cA' => ['type' => ASN1::TYPE_BOOLEAN, 'optional' => \true, 'default' => \false], 'pathLenConstraint' => ['type' => ASN1::TYPE_INTEGER, 'optional' => \true]]];
}
