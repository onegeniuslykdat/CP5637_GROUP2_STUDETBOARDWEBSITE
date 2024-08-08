<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidV2;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
interface DceSecurityGeneratorInterface
{
    /**
     * @param int $localDomain
     * @param IntegerObject|null $localIdentifier
     * @param Hexadecimal|null $node
     * @param int|null $clockSeq
     */
    public function generate($localDomain, $localIdentifier = null, $node = null, $clockSeq = null): string;
}
