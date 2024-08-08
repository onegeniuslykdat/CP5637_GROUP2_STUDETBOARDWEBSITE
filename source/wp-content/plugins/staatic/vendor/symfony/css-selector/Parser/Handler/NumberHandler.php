<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerPatterns;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
class NumberHandler implements HandlerInterface
{
    /**
     * @var TokenizerPatterns
     */
    private $patterns;
    public function __construct(TokenizerPatterns $patterns)
    {
        $this->patterns = $patterns;
    }
    /**
     * @param Reader $reader
     * @param TokenStream $stream
     */
    public function handle($reader, $stream): bool
    {
        $match = $reader->findPattern($this->patterns->getNumberPattern());
        if (!$match) {
            return \false;
        }
        $stream->push(new Token(Token::TYPE_NUMBER, $match[0], $reader->getPosition()));
        $reader->moveForward(\strlen($match[0]));
        return \true;
    }
}
