<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Guid;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\FieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\MaxTrait;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\NilTrait;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\VariantTrait;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\VersionTrait;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use function bin2hex;
use function dechex;
use function hexdec;
use function pack;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;
use function unpack;
use const STR_PAD_LEFT;
final class Fields implements FieldsInterface
{
    /**
     * @var string
     */
    private $bytes;
    use MaxTrait;
    use NilTrait;
    use SerializableFieldsTrait;
    use VariantTrait;
    use VersionTrait;
    public function __construct(string $bytes)
    {
        $this->bytes = $bytes;
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException('The byte string must be 16 bytes long; ' . 'received ' . strlen($this->bytes) . ' bytes');
        }
        if (!$this->isCorrectVariant()) {
            throw new InvalidArgumentException('The byte string received does not conform to the RFC ' . '4122 or Microsoft Corporation variants');
        }
        if (!$this->isCorrectVersion()) {
            throw new InvalidArgumentException('The byte string received does not contain a valid version');
        }
    }
    public function getBytes(): string
    {
        return $this->bytes;
    }
    public function getTimeLow(): Hexadecimal
    {
        $hex = unpack('H*', pack('v*', hexdec(bin2hex(substr($this->bytes, 2, 2))), hexdec(bin2hex(substr($this->bytes, 0, 2)))));
        return new Hexadecimal((string) ($hex[1] ?? ''));
    }
    public function getTimeMid(): Hexadecimal
    {
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 4, 2)))));
        return new Hexadecimal((string) ($hex[1] ?? ''));
    }
    public function getTimeHiAndVersion(): Hexadecimal
    {
        $hex = unpack('H*', pack('v', hexdec(bin2hex(substr($this->bytes, 6, 2)))));
        return new Hexadecimal((string) ($hex[1] ?? ''));
    }
    public function getTimestamp(): Hexadecimal
    {
        return new Hexadecimal(sprintf('%03x%04s%08s', hexdec($this->getTimeHiAndVersion()->toString()) & 0xfff, $this->getTimeMid()->toString(), $this->getTimeLow()->toString()));
    }
    public function getClockSeq(): Hexadecimal
    {
        if ($this->isMax()) {
            $clockSeq = 0xffff;
        } elseif ($this->isNil()) {
            $clockSeq = 0x0;
        } else {
            $clockSeq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
        }
        return new Hexadecimal(str_pad(dechex($clockSeq), 4, '0', STR_PAD_LEFT));
    }
    public function getClockSeqHiAndReserved(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 8, 1)));
    }
    public function getClockSeqLow(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 9, 1)));
    }
    public function getNode(): Hexadecimal
    {
        return new Hexadecimal(bin2hex(substr($this->bytes, 10)));
    }
    public function getVersion(): ?int
    {
        if ($this->isNil() || $this->isMax()) {
            return null;
        }
        $parts = unpack('n*', $this->bytes);
        return (int) $parts[4] >> 4 & 0xf;
    }
    private function isCorrectVariant(): bool
    {
        if ($this->isNil() || $this->isMax()) {
            return \true;
        }
        $variant = $this->getVariant();
        return $variant === Uuid::RFC_4122 || $variant === Uuid::RESERVED_MICROSOFT;
    }
}
