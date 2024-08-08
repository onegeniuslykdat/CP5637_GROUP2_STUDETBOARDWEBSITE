<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider;

use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
interface NodeProviderInterface
{
    public function getNode(): Hexadecimal;
}
