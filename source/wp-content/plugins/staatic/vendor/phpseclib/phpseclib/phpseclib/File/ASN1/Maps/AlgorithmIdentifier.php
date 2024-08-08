<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AlgorithmIdentifier
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['algorithm' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'parameters' => ['type' => ASN1::TYPE_ANY, 'optional' => \true]]];
}
