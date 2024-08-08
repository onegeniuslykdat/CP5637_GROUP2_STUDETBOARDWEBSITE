<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PrivateKeyUsagePeriod
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['notBefore' => ['constant' => 0, 'optional' => \true, 'implicit' => \true, 'type' => ASN1::TYPE_GENERALIZED_TIME], 'notAfter' => ['constant' => 1, 'optional' => \true, 'implicit' => \true, 'type' => ASN1::TYPE_GENERALIZED_TIME]]];
}
