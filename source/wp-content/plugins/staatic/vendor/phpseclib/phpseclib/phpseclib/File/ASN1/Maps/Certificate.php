<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Certificate
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['tbsCertificate' => TBSCertificate::MAP, 'signatureAlgorithm' => AlgorithmIdentifier::MAP, 'signature' => ['type' => ASN1::TYPE_BIT_STRING]]];
}
