<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PKCS9String
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'children' => ['ia5String' => ['type' => ASN1::TYPE_IA5_STRING], 'directoryString' => DirectoryString::MAP]];
}
