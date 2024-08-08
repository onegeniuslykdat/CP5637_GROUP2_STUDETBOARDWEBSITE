<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class RDNSequence
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'min' => 0, 'max' => -1, 'children' => RelativeDistinguishedName::MAP];
}
