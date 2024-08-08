<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class NoticeReference
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['organization' => DisplayText::MAP, 'noticeNumbers' => ['type' => ASN1::TYPE_SEQUENCE, 'min' => 1, 'max' => 200, 'children' => ['type' => ASN1::TYPE_INTEGER]]]];
}
