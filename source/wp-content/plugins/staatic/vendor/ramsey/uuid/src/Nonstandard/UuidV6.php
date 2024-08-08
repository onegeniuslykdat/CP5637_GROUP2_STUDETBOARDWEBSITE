<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Nonstandard;

use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Lazy\LazyUuidFromString;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\TimeTrait;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidInterface;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidV1;
use Staatic\Vendor\Ramsey\Uuid\Uuid as BaseUuid;
class UuidV6 extends BaseUuid implements UuidInterface
{
    use TimeTrait;
    /**
     * @param mixed $fields
     */
    public function __construct($fields, NumberConverterInterface $numberConverter, CodecInterface $codec, TimeConverterInterface $timeConverter)
    {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_REORDERED_TIME) {
            throw new InvalidArgumentException('Fields used to create a UuidV6 must represent a ' . 'version 6 (reordered time) UUID');
        }
        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
    public function toUuidV1(): UuidV1
    {
        $hex = $this->getHex()->toString();
        $hex = substr($hex, 7, 5) . substr($hex, 13, 3) . substr($hex, 3, 4) . '1' . substr($hex, 0, 3) . substr($hex, 16);
        $uuid = Uuid::fromBytes((string) hex2bin($hex));
        return $uuid->toUuidV1();
    }
    /**
     * @param UuidV1 $uuidV1
     */
    public static function fromUuidV1($uuidV1): \Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidV6
    {
        $hex = $uuidV1->getHex()->toString();
        $hex = substr($hex, 13, 3) . substr($hex, 8, 4) . substr($hex, 0, 5) . '6' . substr($hex, 5, 3) . substr($hex, 16);
        $uuid = Uuid::fromBytes((string) hex2bin($hex));
        return $uuid->toUuidV6();
    }
}
