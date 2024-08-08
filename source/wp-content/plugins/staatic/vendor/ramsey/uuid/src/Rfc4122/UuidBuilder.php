<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use Staatic\Vendor\Ramsey\Uuid\Builder\UuidBuilderInterface;
use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\Time\UnixTimeConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnsupportedOperationException;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidInterface as Rfc4122UuidInterface;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use Throwable;
class UuidBuilder implements UuidBuilderInterface
{
    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;
    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;
    /**
     * @var TimeConverterInterface
     */
    private $unixTimeConverter;
    public function __construct(NumberConverterInterface $numberConverter, TimeConverterInterface $timeConverter, ?TimeConverterInterface $unixTimeConverter = null)
    {
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
        $this->unixTimeConverter = $unixTimeConverter ?? new UnixTimeConverter(new BrickMathCalculator());
    }
    /**
     * @param CodecInterface $codec
     * @param string $bytes
     */
    public function build($codec, $bytes): UuidInterface
    {
        try {
            $fields = $this->buildFields($bytes);
            if ($fields->isNil()) {
                return new NilUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
            }
            if ($fields->isMax()) {
                return new MaxUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
            }
            switch ($fields->getVersion()) {
                case Uuid::UUID_TYPE_TIME:
                    return new UuidV1($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_DCE_SECURITY:
                    return new UuidV2($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_HASH_MD5:
                    return new UuidV3($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_RANDOM:
                    return new UuidV4($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_HASH_SHA1:
                    return new UuidV5($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_REORDERED_TIME:
                    return new UuidV6($fields, $this->numberConverter, $codec, $this->timeConverter);
                case Uuid::UUID_TYPE_UNIX_TIME:
                    return new UuidV7($fields, $this->numberConverter, $codec, $this->unixTimeConverter);
                case Uuid::UUID_TYPE_CUSTOM:
                    return new UuidV8($fields, $this->numberConverter, $codec, $this->timeConverter);
            }
            throw new UnsupportedOperationException('The UUID version in the given fields is not supported ' . 'by this UUID builder');
        } catch (Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
    /**
     * @param string $bytes
     */
    protected function buildFields($bytes): FieldsInterface
    {
        return new Fields($bytes);
    }
}
