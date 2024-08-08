<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PBMAC1params
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['keyDerivationFunc' => AlgorithmIdentifier::MAP, 'messageAuthScheme' => AlgorithmIdentifier::MAP]];
}
