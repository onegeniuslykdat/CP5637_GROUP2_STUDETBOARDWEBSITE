<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class DirectoryString
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'children' => ['teletexString' => ['type' => ASN1::TYPE_TELETEX_STRING], 'printableString' => ['type' => ASN1::TYPE_PRINTABLE_STRING], 'universalString' => ['type' => ASN1::TYPE_UNIVERSAL_STRING], 'utf8String' => ['type' => ASN1::TYPE_UTF8_STRING], 'bmpString' => ['type' => ASN1::TYPE_BMP_STRING]]];
}
