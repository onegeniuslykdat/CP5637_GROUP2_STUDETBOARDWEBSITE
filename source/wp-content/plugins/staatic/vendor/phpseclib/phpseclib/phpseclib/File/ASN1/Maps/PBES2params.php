<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PBES2params
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['keyDerivationFunc' => AlgorithmIdentifier::MAP, 'encryptionScheme' => AlgorithmIdentifier::MAP]];
}
