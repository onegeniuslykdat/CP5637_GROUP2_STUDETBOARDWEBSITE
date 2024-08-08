<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

interface PromisorInterface
{
    public function promise(): PromiseInterface;
}
