<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
interface DeprecatedUuidInterface
{
    public function getNumberConverter(): NumberConverterInterface;
    public function getFieldsHex(): array;
    public function getClockSeqHiAndReservedHex(): string;
    public function getClockSeqLowHex(): string;
    public function getClockSequenceHex(): string;
    public function getDateTime(): DateTimeInterface;
    public function getLeastSignificantBitsHex(): string;
    public function getMostSignificantBitsHex(): string;
    public function getNodeHex(): string;
    public function getTimeHiAndVersionHex(): string;
    public function getTimeLowHex(): string;
    public function getTimeMidHex(): string;
    public function getTimestampHex(): string;
    public function getVariant(): ?int;
    public function getVersion(): ?int;
}
