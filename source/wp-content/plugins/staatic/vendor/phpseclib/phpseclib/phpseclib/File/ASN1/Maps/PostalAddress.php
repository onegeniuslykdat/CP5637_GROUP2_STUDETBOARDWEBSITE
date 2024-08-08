<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PostalAddress
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'optional' => \true, 'min' => 1, 'max' => -1, 'children' => DirectoryString::MAP];
}
