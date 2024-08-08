<?php

namespace Staatic\Framework;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class Resource
{
    /**
     * @var StreamInterface
     */
    private $content;
    /**
     * @var string
     */
    private $md5;
    /**
     * @var string
     */
    private $sha1;
    /**
     * @var int
     */
    private $size;
    private function __construct(StreamInterface $content, string $md5, string $sha1, int $size)
    {
        $this->content = $content;
        $this->md5 = $md5;
        $this->sha1 = $sha1;
        $this->size = $size;
    }
    public static function create($content): self
    {
        $content = Utils::streamFor($content);
        [$md5, $sha1, $size] = self::calculateHashesAndSize($content);
        return new self($content, $md5, $sha1, $size);
    }
    public function content(): StreamInterface
    {
        return $this->content;
    }
    public function md5(): string
    {
        return $this->md5;
    }
    public function sha1(): string
    {
        return $this->sha1;
    }
    public function size(): int
    {
        return $this->size;
    }
    public function replace(StreamInterface $content, ?string $md5 = null, ?string $sha1 = null, ?int $size = null): void
    {
        if (!$md5 || !$sha1 || !$size) {
            [$calculatedMd5, $calculatedSha1, $calculatedSize] = self::calculateHashesAndSize($content);
            $md5 = $md5 ?: $calculatedMd5;
            $sha1 = $sha1 ?: $calculatedSha1;
            $size = $size ?: $calculatedSize;
        }
        $this->content = $content;
        $this->md5 = $md5;
        $this->sha1 = $sha1;
        $this->size = $size;
    }
    private static function calculateHashesAndSize(StreamInterface $content): array
    {
        $md5Context = hash_init('md5');
        $sha1Context = hash_init('sha1');
        $size = 0;
        while (!$content->eof()) {
            $buffer = $content->read(1048576);
            hash_update($md5Context, $buffer);
            hash_update($sha1Context, $buffer);
            $size += strlen($buffer);
        }
        $content->rewind();
        return [hash_final($md5Context), hash_final($sha1Context), $size];
    }
}
