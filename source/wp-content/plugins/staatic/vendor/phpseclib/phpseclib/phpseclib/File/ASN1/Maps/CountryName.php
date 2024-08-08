<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class CountryName
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'class' => ASN1::CLASS_APPLICATION, 'cast' => 1, 'children' => ['x121-dcc-code' => ['type' => ASN1::TYPE_NUMERIC_STRING], 'iso-3166-alpha2-code' => ['type' => ASN1::TYPE_PRINTABLE_STRING]]];
}
