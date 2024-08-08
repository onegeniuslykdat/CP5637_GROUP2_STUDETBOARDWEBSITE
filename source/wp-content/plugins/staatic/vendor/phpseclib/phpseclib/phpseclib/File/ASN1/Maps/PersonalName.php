<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PersonalName
{
    const MAP = ['type' => ASN1::TYPE_SET, 'children' => ['surname' => ['type' => ASN1::TYPE_PRINTABLE_STRING, 'constant' => 0, 'optional' => \true, 'implicit' => \true], 'given-name' => ['type' => ASN1::TYPE_PRINTABLE_STRING, 'constant' => 1, 'optional' => \true, 'implicit' => \true], 'initials' => ['type' => ASN1::TYPE_PRINTABLE_STRING, 'constant' => 2, 'optional' => \true, 'implicit' => \true], 'generation-qualifier' => ['type' => ASN1::TYPE_PRINTABLE_STRING, 'constant' => 3, 'optional' => \true, 'implicit' => \true]]];
}
