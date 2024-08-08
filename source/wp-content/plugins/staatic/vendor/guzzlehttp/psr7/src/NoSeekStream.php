<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use RuntimeException;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class NoSeekStream implements StreamInterface
{
    use StreamDecoratorTrait;
    private $stream;
    public function seek($offset, $whence = \SEEK_SET): void
    {
        throw new RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable(): bool
    {
        return \false;
    }
}
