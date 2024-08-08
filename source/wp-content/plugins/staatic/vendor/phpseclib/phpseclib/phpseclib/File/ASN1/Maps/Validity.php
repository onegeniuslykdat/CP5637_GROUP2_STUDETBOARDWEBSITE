<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Validity
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['notBefore' => Time::MAP, 'notAfter' => Time::MAP]];
}
