<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PolicyQualifierInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['policyQualifierId' => PolicyQualifierId::MAP, 'qualifier' => ['type' => ASN1::TYPE_ANY]]];
}
