<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PolicyInformation
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['policyIdentifier' => CertPolicyId::MAP, 'policyQualifiers' => ['type' => ASN1::TYPE_SEQUENCE, 'min' => 0, 'max' => -1, 'optional' => \true, 'children' => PolicyQualifierInfo::MAP]]];
}
