<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient;

use Iterator;
interface ResponseStreamInterface extends Iterator
{
    public function key(): ResponseInterface;
    public function current(): ChunkInterface;
}
