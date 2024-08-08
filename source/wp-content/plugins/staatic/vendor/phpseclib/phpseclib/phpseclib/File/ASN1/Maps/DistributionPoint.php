<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DistributionPoint
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['distributionPoint' => ['constant' => 0, 'optional' => \true, 'explicit' => \true] + DistributionPointName::MAP, 'reasons' => ['constant' => 1, 'optional' => \true, 'implicit' => \true] + ReasonFlags::MAP, 'cRLIssuer' => ['constant' => 2, 'optional' => \true, 'implicit' => \true] + GeneralNames::MAP]];
}
