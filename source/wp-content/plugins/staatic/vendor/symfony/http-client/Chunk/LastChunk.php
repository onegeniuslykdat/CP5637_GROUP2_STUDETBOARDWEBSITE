<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Chunk;

class LastChunk extends DataChunk
{
    public function isLast(): bool
    {
        return \true;
    }
}
