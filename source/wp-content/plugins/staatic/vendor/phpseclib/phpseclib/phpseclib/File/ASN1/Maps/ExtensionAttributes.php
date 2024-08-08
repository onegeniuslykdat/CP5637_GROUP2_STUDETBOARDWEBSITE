<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class ExtensionAttributes
{
    const MAP = ['type' => ASN1::TYPE_SET, 'min' => 1, 'max' => 256, 'children' => ExtensionAttribute::MAP];
}
