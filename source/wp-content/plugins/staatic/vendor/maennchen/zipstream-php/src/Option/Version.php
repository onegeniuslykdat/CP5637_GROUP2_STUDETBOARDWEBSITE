<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use Staatic\Vendor\MyCLabs\Enum\Enum;
class Version extends Enum
{
    public const STORE = 0xa;
    public const DEFLATE = 0x14;
    public const ZIP64 = 0x2d;
}
