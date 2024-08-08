<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

interface NumberInterface extends TypeInterface
{
    public function isNegative(): bool;
}
