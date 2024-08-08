<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Chunk;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
class DataChunk implements ChunkInterface
{
    /**
     * @var int
     */
    private $offset = 0;
    /**
     * @var string
     */
    private $content = '';
    public function __construct(int $offset = 0, string $content = '')
    {
        $this->offset = $offset;
        $this->content = $content;
    }
    public function isTimeout(): bool
    {
        return \false;
    }
    public function isFirst(): bool
    {
        return \false;
    }
    public function isLast(): bool
    {
        return \false;
    }
    public function getInformationalStatus(): ?array
    {
        return null;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getOffset(): int
    {
        return $this->offset;
    }
    public function getError(): ?string
    {
        return null;
    }
}
