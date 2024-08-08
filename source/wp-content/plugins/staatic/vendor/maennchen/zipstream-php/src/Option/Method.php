<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use Staatic\Vendor\MyCLabs\Enum\Enum;
class Method extends Enum
{
    public const STORE = 0x0;
    public const DEFLATE = 0x8;
}
