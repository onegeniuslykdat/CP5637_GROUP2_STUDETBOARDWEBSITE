<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use JsonSerializable;
use Serializable;
interface TypeInterface extends JsonSerializable, Serializable
{
    public function toString(): string;
    public function __toString(): string;
}
