<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use function uuid_create;
use function uuid_parse;
use const UUID_TYPE_RANDOM;
class PeclUuidRandomGenerator implements RandomGeneratorInterface
{
    /**
     * @param int $length
     */
    public function generate($length): string
    {
        $uuid = uuid_create(UUID_TYPE_RANDOM);
        return uuid_parse($uuid);
    }
}
