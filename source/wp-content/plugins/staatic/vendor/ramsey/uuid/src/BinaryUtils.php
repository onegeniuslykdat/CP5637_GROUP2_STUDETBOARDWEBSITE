<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

class BinaryUtils
{
    /**
     * @param int $clockSeq
     */
    public static function applyVariant($clockSeq): int
    {
        $clockSeq = $clockSeq & 0x3fff;
        $clockSeq |= 0x8000;
        return $clockSeq;
    }
    /**
     * @param int $timeHi
     * @param int $version
     */
    public static function applyVersion($timeHi, $version): int
    {
        $timeHi = $timeHi & 0xfff;
        $timeHi |= $version << 12;
        return $timeHi;
    }
}
