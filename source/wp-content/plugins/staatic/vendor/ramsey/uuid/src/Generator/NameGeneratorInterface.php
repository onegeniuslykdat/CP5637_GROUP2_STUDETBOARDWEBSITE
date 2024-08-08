<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
interface NameGeneratorInterface
{
    /**
     * @param UuidInterface $ns
     * @param string $name
     * @param string $hashAlgorithm
     */
    public function generate($ns, $name, $hashAlgorithm): string;
}
