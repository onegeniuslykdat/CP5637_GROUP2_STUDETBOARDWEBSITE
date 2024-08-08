<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class AdministrationDomainName
{
    const MAP = ['type' => ASN1::TYPE_CHOICE, 'class' => ASN1::CLASS_APPLICATION, 'cast' => 2, 'children' => ['numeric' => ['type' => ASN1::TYPE_NUMERIC_STRING], 'printable' => ['type' => ASN1::TYPE_PRINTABLE_STRING]]];
}
