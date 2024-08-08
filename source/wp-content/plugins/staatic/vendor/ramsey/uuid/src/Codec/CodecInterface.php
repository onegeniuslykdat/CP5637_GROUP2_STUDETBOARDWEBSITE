<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Codec;

use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
interface CodecInterface
{
    /**
     * @param UuidInterface $uuid
     */
    public function encode($uuid): string;
    /**
     * @param UuidInterface $uuid
     */
    public function encodeBinary($uuid): string;
    /**
     * @param string $encodedUuid
     */
    public function decode($encodedUuid): UuidInterface;
    /**
     * @param string $bytes
     */
    public function decodeBytes($bytes): UuidInterface;
}
