<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AuthorityKeyIdentifier
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['keyIdentifier' => ['constant' => 0, 'optional' => \true, 'implicit' => \true] + KeyIdentifier::MAP, 'authorityCertIssuer' => ['constant' => 1, 'optional' => \true, 'implicit' => \true] + GeneralNames::MAP, 'authorityCertSerialNumber' => ['constant' => 2, 'optional' => \true, 'implicit' => \true] + CertificateSerialNumber::MAP]];
}
