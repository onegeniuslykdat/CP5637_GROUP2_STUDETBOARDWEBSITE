<?php

namespace Staatic\Vendor\phpseclib3\File\ASN1\Maps;

use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class RSASSA_PSS_params
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['hashAlgorithm' => ['constant' => 0, 'optional' => \true, 'explicit' => \true] + HashAlgorithm::MAP, 'maskGenAlgorithm' => ['constant' => 1, 'optional' => \true, 'explicit' => \true] + MaskGenAlgorithm::MAP, 'saltLength' => ['type' => ASN1::TYPE_INTEGER, 'constant' => 2, 'optional' => \true, 'explicit' => \true, 'default' => 20], 'trailerField' => ['type' => ASN1::TYPE_INTEGER, 'constant' => 3, 'optional' => \true, 'explicit' => \true, 'default' => 1]]];
}
