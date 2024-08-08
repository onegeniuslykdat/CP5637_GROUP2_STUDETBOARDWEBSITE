<?php

namespace Staatic\Vendor\phpseclib3\Math\Common\FiniteField;

use JsonSerializable;
use ReturnTypeWillChange;
abstract class Integer implements JsonSerializable
{
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['hex' => $this->toHex(\true)];
    }
    abstract public function toHex();
}
