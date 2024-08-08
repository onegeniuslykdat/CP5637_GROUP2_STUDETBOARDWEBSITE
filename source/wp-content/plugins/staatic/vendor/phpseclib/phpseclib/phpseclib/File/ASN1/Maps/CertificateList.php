<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class CertificateList
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['tbsCertList' => TBSCertList::MAP, 'signatureAlgorithm' => AlgorithmIdentifier::MAP, 'signature' => ['type' => ASN1::TYPE_BIT_STRING]]];
}
