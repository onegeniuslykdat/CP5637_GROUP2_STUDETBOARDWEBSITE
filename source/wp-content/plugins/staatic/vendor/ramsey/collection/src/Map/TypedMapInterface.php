<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

interface TypedMapInterface extends MapInterface
{
    public function getKeyType(): string;
    public function getValueType(): string;
}
