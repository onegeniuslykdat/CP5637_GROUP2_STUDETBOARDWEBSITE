<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PublicKeyAndChallenge
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['spki' => SubjectPublicKeyInfo::MAP, 'challenge' => ['type' => ASN1::TYPE_IA5_STRING]]];
}
