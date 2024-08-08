<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Nonstandard;

use Staatic\Vendor\Ramsey\Uuid\Builder\UuidBuilderInterface;
use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\UnableToBuildUuidException;
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
    public function __construct(NumberConverterInterface $numberConverter, TimeConverterInterface $timeConverter)
    {
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }
    /**
     * @param CodecInterface $codec
     * @param string $bytes
     */
    public function build($codec, $bytes): UuidInterface
    {
        try {
            return new Uuid($this->buildFields($bytes), $this->numberConverter, $codec, $this->timeConverter);
        } catch (Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
    /**
     * @param string $bytes
     */
    protected function buildFields($bytes): Fields
    {
        return new Fields($bytes);
    }
}
