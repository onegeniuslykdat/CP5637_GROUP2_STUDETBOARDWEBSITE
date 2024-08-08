<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class SubjectPublicKeyInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['algorithm' => AlgorithmIdentifier::MAP, 'subjectPublicKey' => ['type' => ASN1::TYPE_BIT_STRING]]];
}
