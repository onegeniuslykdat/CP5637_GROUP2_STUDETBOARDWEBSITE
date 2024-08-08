<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
interface ChunkInterface
{
    public function isTimeout(): bool;
    public function isFirst(): bool;
    public function isLast(): bool;
    public function getInformationalStatus(): ?array;
    public function getContent(): string;
    public function getOffset(): int;
    public function getError(): ?string;
}
