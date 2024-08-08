<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use JsonSerializable;
use Staatic\Vendor\Ramsey\Uuid\Fields\FieldsInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Serializable;
use Stringable;
interface UuidInterface extends DeprecatedUuidInterface, JsonSerializable, Serializable, Stringable
{
    /**
     * @param \Staatic\Vendor\Ramsey\Uuid\UuidInterface $other
     */
    public function compareTo($other): int;
    /**
     * @param object|null $other
     */
    public function equals($other): bool;
    public function getBytes(): string;
    public function getFields();
    public function getHex(): Hexadecimal;
    public function getInteger();
    public function getUrn(): string;
    public function toString(): string;
    public function __toString(): string;
}
