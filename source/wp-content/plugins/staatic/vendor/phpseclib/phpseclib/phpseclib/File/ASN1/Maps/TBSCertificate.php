<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class TBSCertificate
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => ASN1::TYPE_INTEGER, 'constant' => 0, 'optional' => \true, 'explicit' => \true, 'mapping' => ['v1', 'v2', 'v3'], 'default' => 'v1'], 'serialNumber' => CertificateSerialNumber::MAP, 'signature' => AlgorithmIdentifier::MAP, 'issuer' => Name::MAP, 'validity' => Validity::MAP, 'subject' => Name::MAP, 'subjectPublicKeyInfo' => SubjectPublicKeyInfo::MAP, 'issuerUniqueID' => ['constant' => 1, 'optional' => \true, 'implicit' => \true] + UniqueIdentifier::MAP, 'subjectUniqueID' => ['constant' => 2, 'optional' => \true, 'implicit' => \true] + UniqueIdentifier::MAP, 'extensions' => ['constant' => 3, 'optional' => \true, 'explicit' => \true] + Extensions::MAP]];
}
