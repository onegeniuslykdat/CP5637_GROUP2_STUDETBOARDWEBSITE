<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PublicKeyInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['publicKeyAlgorithm' => AlgorithmIdentifier::MAP, 'publicKey' => ['type' => ASN1::TYPE_BIT_STRING]]];
}
