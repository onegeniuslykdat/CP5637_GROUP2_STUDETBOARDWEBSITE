<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
class CommentHandler implements HandlerInterface
{
    /**
     * @param Reader $reader
     * @param TokenStream $stream
     */
    public function handle($reader, $stream): bool
    {
        if ('/*' !== $reader->getSubstring(2)) {
            return \false;
        }
        $offset = $reader->getOffset('*/');
        if (\false === $offset) {
            $reader->moveToEnd();
        } else {
            $reader->moveForward($offset + 2);
        }
        return \true;
    }
}
