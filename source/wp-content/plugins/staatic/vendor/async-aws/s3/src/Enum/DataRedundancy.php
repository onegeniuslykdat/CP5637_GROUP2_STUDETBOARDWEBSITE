<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class DataRedundancy
{
    public const SINGLE_AVAILABILITY_ZONE = 'SingleAvailabilityZone';
    public static function exists(string $value): bool
    {
        return isset([self::SINGLE_AVAILABILITY_ZONE => \true][$value]);
    }
}
