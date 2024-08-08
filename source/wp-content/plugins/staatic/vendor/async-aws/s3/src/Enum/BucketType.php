<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class BucketType
{
    public const DIRECTORY = 'Directory';
    public static function exists(string $value): bool
    {
        return isset([self::DIRECTORY => \true][$value]);
    }
}
