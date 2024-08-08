<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Validator\ValidatorInterface;
interface UuidFactoryInterface
{
    /**
     * @param string $bytes
     */
    public function fromBytes($bytes): UuidInterface;
    /**
     * @param DateTimeInterface $dateTime
     * @param Hexadecimal|null $node
     * @param int|null $clockSeq
     */
    public function fromDateTime($dateTime, $node = null, $clockSeq = null): UuidInterface;
    /**
     * @param string $integer
     */
    public function fromInteger($integer): UuidInterface;
    /**
     * @param string $uuid
     */
    public function fromString($uuid): UuidInterface;
    public function getValidator(): ValidatorInterface;
    /**
     * @param int|null $clockSeq
     */
    public function uuid1($node = null, $clockSeq = null): UuidInterface;
    /**
     * @param int $localDomain
     * @param IntegerObject|null $localIdentifier
     * @param Hexadecimal|null $node
     * @param int|null $clockSeq
     */
    public function uuid2($localDomain, $localIdentifier = null, $node = null, $clockSeq = null): UuidInterface;
    /**
     * @param string $name
     */
    public function uuid3($ns, $name): UuidInterface;
    public function uuid4(): UuidInterface;
    /**
     * @param string $name
     */
    public function uuid5($ns, $name): UuidInterface;
    /**
     * @param Hexadecimal|null $node
     * @param int|null $clockSeq
     */
    public function uuid6($node = null, $clockSeq = null): UuidInterface;
}
