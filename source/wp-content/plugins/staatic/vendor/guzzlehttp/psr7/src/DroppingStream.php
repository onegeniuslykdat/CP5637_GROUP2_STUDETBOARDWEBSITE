<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class DroppingStream implements StreamInterface
{
    use StreamDecoratorTrait;
    private $maxLength;
    private $stream;
    public function __construct(StreamInterface $stream, int $maxLength)
    {
        $this->stream = $stream;
        $this->maxLength = $maxLength;
    }
    public function write($string): int
    {
        $diff = $this->maxLength - $this->stream->getSize();
        if ($diff <= 0) {
            return 0;
        }
        if (strlen($string) < $diff) {
            return $this->stream->write($string);
        }
        return $this->stream->write(substr($string, 0, $diff));
    }
}
