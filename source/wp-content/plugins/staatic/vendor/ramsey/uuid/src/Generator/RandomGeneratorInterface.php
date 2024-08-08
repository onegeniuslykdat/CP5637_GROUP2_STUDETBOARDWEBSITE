<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

interface RandomGeneratorInterface
{
    /**
     * @param int $length
     */
    public function generate($length): string;
}
