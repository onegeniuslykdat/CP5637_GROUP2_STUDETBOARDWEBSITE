<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AttributeTypeAndValue
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['type' => AttributeType::MAP, 'value' => AttributeValue::MAP]];
}
