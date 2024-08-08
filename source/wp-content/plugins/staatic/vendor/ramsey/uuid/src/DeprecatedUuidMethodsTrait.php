<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\DateTimeException;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnsupportedOperationException;
use Throwable;
use function str_pad;
use function substr;
use const STR_PAD_LEFT;
trait DeprecatedUuidMethodsTrait
{
    public function getClockSeqHiAndReserved(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqHiAndReserved()->toString());
    }
    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->fields->getClockSeqHiAndReserved()->toString();
    }
    public function getClockSeqLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqLow()->toString());
    }
    public function getClockSeqLowHex(): string
    {
        return $this->fields->getClockSeqLow()->toString();
    }
    public function getClockSequence(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeq()->toString());
    }
    public function getClockSequenceHex(): string
    {
        return $this->fields->getClockSeq()->toString();
    }
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }
    public function getDateTime(): DateTimeInterface
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }
        $time = $this->timeConverter->convertTime($this->fields->getTimestamp());
        try {
            return new DateTimeImmutable('@' . $time->getSeconds()->toString() . '.' . str_pad($time->getMicroseconds()->toString(), 6, '0', STR_PAD_LEFT));
        } catch (Throwable $e) {
            throw new DateTimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
    public function getFieldsHex(): array
    {
        return ['time_low' => $this->fields->getTimeLow()->toString(), 'time_mid' => $this->fields->getTimeMid()->toString(), 'time_hi_and_version' => $this->fields->getTimeHiAndVersion()->toString(), 'clock_seq_hi_and_reserved' => $this->fields->getClockSeqHiAndReserved()->toString(), 'clock_seq_low' => $this->fields->getClockSeqLow()->toString(), 'node' => $this->fields->getNode()->toString()];
    }
    public function getLeastSignificantBits(): string
    {
        $leastSignificantHex = substr($this->getHex()->toString(), 16);
        return $this->numberConverter->fromHex($leastSignificantHex);
    }
    public function getLeastSignificantBitsHex(): string
    {
        return substr($this->getHex()->toString(), 16);
    }
    public function getMostSignificantBits(): string
    {
        $mostSignificantHex = substr($this->getHex()->toString(), 0, 16);
        return $this->numberConverter->fromHex($mostSignificantHex);
    }
    public function getMostSignificantBitsHex(): string
    {
        return substr($this->getHex()->toString(), 0, 16);
    }
    public function getNode(): string
    {
        return $this->numberConverter->fromHex($this->fields->getNode()->toString());
    }
    public function getNodeHex(): string
    {
        return $this->fields->getNode()->toString();
    }
    public function getTimeHiAndVersion(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeHiAndVersion()->toString());
    }
    public function getTimeHiAndVersionHex(): string
    {
        return $this->fields->getTimeHiAndVersion()->toString();
    }
    public function getTimeLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeLow()->toString());
    }
    public function getTimeLowHex(): string
    {
        return $this->fields->getTimeLow()->toString();
    }
    public function getTimeMid(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeMid()->toString());
    }
    public function getTimeMidHex(): string
    {
        return $this->fields->getTimeMid()->toString();
    }
    public function getTimestamp(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }
        return $this->numberConverter->fromHex($this->fields->getTimestamp()->toString());
    }
    public function getTimestampHex(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }
        return $this->fields->getTimestamp()->toString();
    }
    public function getVariant(): ?int
    {
        return $this->fields->getVariant();
    }
    public function getVersion(): ?int
    {
        return $this->fields->getVersion();
    }
}
