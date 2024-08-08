<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

class NameGeneratorFactory
{
    public function getGenerator(): NameGeneratorInterface
    {
        return new DefaultNameGenerator();
    }
}
