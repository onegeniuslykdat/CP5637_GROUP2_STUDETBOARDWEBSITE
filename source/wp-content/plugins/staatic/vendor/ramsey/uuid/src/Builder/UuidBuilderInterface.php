<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Builder;

use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\UuidInterface;
interface UuidBuilderInterface
{
    /**
     * @param CodecInterface $codec
     * @param string $bytes
     */
    public function build($codec, $bytes): UuidInterface;
}
