<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
interface TimeGeneratorInterface
{
    /**
     * @param int|null $clockSeq
     */
    public function generate($node = null, $clockSeq = null): string;
}
