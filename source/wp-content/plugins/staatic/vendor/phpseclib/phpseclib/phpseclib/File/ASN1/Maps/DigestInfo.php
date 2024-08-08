<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DigestInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['digestAlgorithm' => AlgorithmIdentifier::MAP, 'digest' => ['type' => ASN1::TYPE_OCTET_STRING]]];
}
