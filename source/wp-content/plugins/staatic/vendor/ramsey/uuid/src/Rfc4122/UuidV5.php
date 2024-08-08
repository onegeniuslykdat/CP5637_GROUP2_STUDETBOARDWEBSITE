<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
final class UuidV5 extends Uuid implements UuidInterface
{
    public function __construct(Rfc4122FieldsInterface $fields, NumberConverterInterface $numberConverter, CodecInterface $codec, TimeConverterInterface $timeConverter)
    {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_HASH_SHA1) {
            throw new InvalidArgumentException('Fields used to create a UuidV5 must represent a ' . 'version 5 (named-based, SHA1-hashed) UUID');
        }
        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
}
