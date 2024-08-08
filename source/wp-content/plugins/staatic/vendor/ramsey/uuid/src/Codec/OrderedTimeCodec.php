<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Codec;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnsupportedOperationException;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use function strlen;
use function substr;
class OrderedTimeCodec extends StringCodec
{
    /**
     * @param UuidInterface $uuid
     */
    public function encodeBinary($uuid): string
    {
        if (!$uuid->getFields() instanceof Rfc4122FieldsInterface || $uuid->getFields()->getVersion() !== Uuid::UUID_TYPE_TIME) {
            throw new InvalidArgumentException('Expected RFC 4122 version 1 (time-based) UUID');
        }
        $bytes = $uuid->getFields()->getBytes();
        return $bytes[6] . $bytes[7] . $bytes[4] . $bytes[5] . $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3] . substr($bytes, 8);
    }
    /**
     * @param string $bytes
     */
    public function decodeBytes($bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }
        $rearrangedBytes = $bytes[4] . $bytes[5] . $bytes[6] . $bytes[7] . $bytes[2] . $bytes[3] . $bytes[0] . $bytes[1] . substr($bytes, 8);
        $uuid = parent::decodeBytes($rearrangedBytes);
        if (!$uuid->getFields() instanceof Rfc4122FieldsInterface || $uuid->getFields()->getVersion() !== Uuid::UUID_TYPE_TIME) {
            throw new UnsupportedOperationException('Attempting to decode a non-time-based UUID using ' . 'OrderedTimeCodec');
        }
        return $uuid;
    }
}
