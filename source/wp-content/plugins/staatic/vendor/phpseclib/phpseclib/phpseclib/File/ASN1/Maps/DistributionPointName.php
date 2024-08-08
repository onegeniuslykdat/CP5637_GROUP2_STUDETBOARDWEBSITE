<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DistributionPointName
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'children' => ['fullName' => ['constant' => 0, 'optional' => \true, 'implicit' => \true] + GeneralNames::MAP, 'nameRelativeToCRLIssuer' => ['constant' => 1, 'optional' => \true, 'implicit' => \true] + RelativeDistinguishedName::MAP]];
}
