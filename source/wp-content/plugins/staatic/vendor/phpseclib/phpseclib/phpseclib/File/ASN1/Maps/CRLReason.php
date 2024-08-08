<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class CRLReason
{
    const MAP = ['type' => ASN1::TYPE_ENUMERATED, 'mapping' => ['unspecified', 'keyCompromise', 'cACompromise', 'affiliationChanged', 'superseded', 'cessationOfOperation', 'certificateHold', 8 => 'removeFromCRL', 'privilegeWithdrawn', 'aACompromise']];
}
