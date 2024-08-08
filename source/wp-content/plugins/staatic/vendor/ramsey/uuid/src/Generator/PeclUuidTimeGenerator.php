<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use function uuid_create;
use function uuid_parse;
use const UUID_TYPE_TIME;
class PeclUuidTimeGenerator implements TimeGeneratorInterface
{
    /**
     * @param int|null $clockSeq
     */
    public function generate($node = null, $clockSeq = null): string
    {
        $uuid = uuid_create(UUID_TYPE_TIME);
        return uuid_parse($uuid);
    }
}
