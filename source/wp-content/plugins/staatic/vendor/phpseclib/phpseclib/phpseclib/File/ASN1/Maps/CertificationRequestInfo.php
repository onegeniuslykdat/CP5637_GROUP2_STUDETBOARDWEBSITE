<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class CertificationRequestInfo
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => ASN1::TYPE_INTEGER, 'mapping' => ['v1']], 'subject' => Name::MAP, 'subjectPKInfo' => SubjectPublicKeyInfo::MAP, 'attributes' => ['constant' => 0, 'optional' => \true, 'implicit' => \true] + Attributes::MAP]];
}
