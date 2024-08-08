<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Codec;

use Staatic\Vendor\Ramsey\Uuid\Builder\UuidBuilderInterface;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidUuidStringException;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
use function bin2hex;
use function hex2bin;
use function implode;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
class StringCodec implements CodecInterface
{
    /**
     * @var UuidBuilderInterface
     */
    private $builder;
    public function __construct(UuidBuilderInterface $builder)
    {
        $this->builder = $builder;
    }
    /**
     * @param UuidInterface $uuid
     */
    public function encode($uuid): string
    {
        $hex = bin2hex($uuid->getFields()->getBytes());
        return sprintf('%08s-%04s-%04s-%04s-%012s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20));
    }
    /**
     * @param UuidInterface $uuid
     */
    public function encodeBinary($uuid): string
    {
        return $uuid->getFields()->getBytes();
    }
    /**
     * @param string $encodedUuid
     */
    public function decode($encodedUuid): UuidInterface
    {
        return $this->builder->build($this, $this->getBytes($encodedUuid));
    }
    /**
     * @param string $bytes
     */
    public function decodeBytes($bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }
        return $this->builder->build($this, $bytes);
    }
    protected function getBuilder(): UuidBuilderInterface
    {
        return $this->builder;
    }
    /**
     * @param string $encodedUuid
     */
    protected function getBytes($encodedUuid): string
    {
        $parsedUuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}', '-'], '', $encodedUuid);
        $components = [substr($parsedUuid, 0, 8), substr($parsedUuid, 8, 4), substr($parsedUuid, 12, 4), substr($parsedUuid, 16, 4), substr($parsedUuid, 20)];
        if (!Uuid::isValid(implode('-', $components))) {
            throw new InvalidUuidStringException('Invalid UUID string: ' . $encodedUuid);
        }
        return (string) hex2bin($parsedUuid);
    }
}
