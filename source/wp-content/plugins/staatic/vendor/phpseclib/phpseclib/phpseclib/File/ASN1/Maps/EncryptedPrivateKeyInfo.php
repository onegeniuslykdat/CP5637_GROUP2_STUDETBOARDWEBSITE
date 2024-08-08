<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class EncryptedPrivateKeyInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['encryptionAlgorithm' => AlgorithmIdentifier::MAP, 'encryptedData' => EncryptedData::MAP]];
}
