<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Generator;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
final class ResponseStream implements ResponseStreamInterface
{
    /**
     * @var Generator
     */
    private $generator;
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }
    public function key(): ResponseInterface
    {
        return $this->generator->key();
    }
    public function current(): ChunkInterface
    {
        return $this->generator->current();
    }
    public function next(): void
    {
        $this->generator->next();
    }
    public function rewind(): void
    {
        $this->generator->rewind();
    }
    public function valid(): bool
    {
        return $this->generator->valid();
    }
}
