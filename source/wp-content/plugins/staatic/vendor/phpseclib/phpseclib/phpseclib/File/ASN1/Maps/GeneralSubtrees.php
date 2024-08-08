<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class GeneralSubtrees
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'min' => 1, 'max' => -1, 'children' => GeneralSubtree::MAP];
}
