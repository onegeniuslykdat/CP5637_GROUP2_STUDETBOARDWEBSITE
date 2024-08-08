<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Exception\RandomSourceException;
use Throwable;
class RandomBytesGenerator implements RandomGeneratorInterface
{
    /**
     * @param int $length
     */
    public function generate($length): string
    {
        try {
            return random_bytes($length);
        } catch (Throwable $exception) {
            throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}
