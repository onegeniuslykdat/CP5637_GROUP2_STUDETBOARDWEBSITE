<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class ECParameters
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'children' => ['namedCurve' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'implicitCurve' => ['type' => ASN1::TYPE_NULL], 'specifiedCurve' => SpecifiedECDomain::MAP]];
}
