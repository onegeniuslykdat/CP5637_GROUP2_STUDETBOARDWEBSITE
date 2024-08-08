<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class ECPrivateKey
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => ASN1::TYPE_INTEGER, 'mapping' => [1 => 'ecPrivkeyVer1']], 'privateKey' => ['type' => ASN1::TYPE_OCTET_STRING], 'parameters' => ['constant' => 0, 'optional' => \true, 'explicit' => \true] + ECParameters::MAP, 'publicKey' => ['type' => ASN1::TYPE_BIT_STRING, 'constant' => 1, 'optional' => \true, 'explicit' => \true]]];
}
