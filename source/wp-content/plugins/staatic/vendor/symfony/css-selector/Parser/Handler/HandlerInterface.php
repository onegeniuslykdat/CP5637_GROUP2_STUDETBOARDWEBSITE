<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
interface HandlerInterface
{
    /**
     * @param Reader $reader
     * @param TokenStream $stream
     */
    public function handle($reader, $stream): bool;
}
