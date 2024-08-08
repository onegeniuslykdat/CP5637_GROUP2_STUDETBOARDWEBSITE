<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class SpecifiedECDomain
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => ASN1::TYPE_INTEGER, 'mapping' => [1 => 'ecdpVer1', 'ecdpVer2', 'ecdpVer3']], 'fieldID' => FieldID::MAP, 'curve' => Curve::MAP, 'base' => ECPoint::MAP, 'order' => ['type' => ASN1::TYPE_INTEGER], 'cofactor' => ['type' => ASN1::TYPE_INTEGER, 'optional' => \true], 'hash' => ['optional' => \true] + HashAlgorithm::MAP]];
}
