<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class Attribute
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['type' => AttributeType::MAP, 'value' => ['type' => ASN1::TYPE_SET, 'min' => 1, 'max' => -1, 'children' => AttributeValue::MAP]]];
}
