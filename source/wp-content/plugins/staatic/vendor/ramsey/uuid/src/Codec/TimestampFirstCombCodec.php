<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Codec;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidUuidStringException;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use function bin2hex;
use function sprintf;
use function substr;
use function substr_replace;
class TimestampFirstCombCodec extends StringCodec
{
    /**
     * @param UuidInterface $uuid
     */
    public function encode($uuid): string
    {
        $bytes = $this->swapBytes($uuid->getFields()->getBytes());
        return sprintf('%08s-%04s-%04s-%04s-%012s', bin2hex(substr($bytes, 0, 4)), bin2hex(substr($bytes, 4, 2)), bin2hex(substr($bytes, 6, 2)), bin2hex(substr($bytes, 8, 2)), bin2hex(substr($bytes, 10)));
    }
    /**
     * @param UuidInterface $uuid
     */
    public function encodeBinary($uuid): string
    {
        return $this->swapBytes($uuid->getFields()->getBytes());
    }
    /**
     * @param string $encodedUuid
     */
    public function decode($encodedUuid): UuidInterface
    {
        $bytes = $this->getBytes($encodedUuid);
        return $this->getBuilder()->build($this, $this->swapBytes($bytes));
    }
    /**
     * @param string $bytes
     */
    public function decodeBytes($bytes): UuidInterface
    {
        return $this->getBuilder()->build($this, $this->swapBytes($bytes));
    }
    private function swapBytes(string $bytes): string
    {
        $first48Bits = substr($bytes, 0, 6);
        $last48Bits = substr($bytes, -6);
        $bytes = substr_replace($bytes, $last48Bits, 0, 6);
        $bytes = substr_replace($bytes, $first48Bits, -6);
        return $bytes;
    }
}
