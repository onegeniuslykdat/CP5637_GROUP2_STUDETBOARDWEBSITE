<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use Staatic\Vendor\Ramsey\Uuid\Fields\FieldsInterface as BaseFieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
interface FieldsInterface extends BaseFieldsInterface
{
    public function getClockSeq(): Hexadecimal;
    public function getClockSeqHiAndReserved(): Hexadecimal;
    public function getClockSeqLow(): Hexadecimal;
    public function getNode(): Hexadecimal;
    public function getTimeHiAndVersion(): Hexadecimal;
    public function getTimeLow(): Hexadecimal;
    public function getTimeMid(): Hexadecimal;
    public function getTimestamp(): Hexadecimal;
    public function getVariant(): int;
    public function getVersion(): ?int;
    public function isNil(): bool;
}
