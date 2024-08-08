<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AccessDescription
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['accessMethod' => ['type' => ASN1::TYPE_OBJECT_IDENTIFIER], 'accessLocation' => GeneralName::MAP]];
}
