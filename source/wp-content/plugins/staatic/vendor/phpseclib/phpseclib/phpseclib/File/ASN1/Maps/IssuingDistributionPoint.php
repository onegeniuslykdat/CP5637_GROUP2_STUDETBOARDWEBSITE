<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class IssuingDistributionPoint
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['distributionPoint' => ['constant' => 0, 'optional' => \true, 'explicit' => \true] + DistributionPointName::MAP, 'onlyContainsUserCerts' => ['type' => ASN1::TYPE_BOOLEAN, 'constant' => 1, 'optional' => \true, 'default' => \false, 'implicit' => \true], 'onlyContainsCACerts' => ['type' => ASN1::TYPE_BOOLEAN, 'constant' => 2, 'optional' => \true, 'default' => \false, 'implicit' => \true], 'onlySomeReasons' => ['constant' => 3, 'optional' => \true, 'implicit' => \true] + ReasonFlags::MAP, 'indirectCRL' => ['type' => ASN1::TYPE_BOOLEAN, 'constant' => 4, 'optional' => \true, 'default' => \false, 'implicit' => \true], 'onlyContainsAttributeCerts' => ['type' => ASN1::TYPE_BOOLEAN, 'constant' => 5, 'optional' => \true, 'default' => \false, 'implicit' => \true]]];
}
