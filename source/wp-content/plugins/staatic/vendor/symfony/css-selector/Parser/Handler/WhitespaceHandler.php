<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
class WhitespaceHandler implements HandlerInterface
{
    /**
     * @param Reader $reader
     * @param TokenStream $stream
     */
    public function handle($reader, $stream): bool
    {
        $match = $reader->findPattern('~^[ \t\r\n\f]+~');
        if (\false === $match) {
            return \false;
        }
        $stream->push(new Token(Token::TYPE_WHITESPACE, $match[0], $reader->getPosition()));
        $reader->moveForward(\strlen($match[0]));
        return \true;
    }
}
