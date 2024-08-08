<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Stream;

interface ResultStream
{
    public function getChunks(): iterable;
    public function getContentAsString(): string;
    public function getContentAsResource();
}
