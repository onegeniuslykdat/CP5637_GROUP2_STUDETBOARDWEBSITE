<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerEscaping;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerPatterns;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
class HashHandler implements HandlerInterface
{
    /**
     * @var TokenizerPatterns
     */
    private $patterns;
    /**
     * @var TokenizerEscaping
     */
    private $escaping;
    public function __construct(TokenizerPatterns $patterns, TokenizerEscaping $escaping)
    {
        $this->patterns = $patterns;
        $this->escaping = $escaping;
    }
    /**
     * @param Reader $reader
     * @param TokenStream $stream
     */
    public function handle($reader, $stream): bool
    {
        $match = $reader->findPattern($this->patterns->getHashPattern());
        if (!$match) {
            return \false;
        }
        $value = $this->escaping->escapeUnicode($match[1]);
        $stream->push(new Token(Token::TYPE_HASH, $value, $reader->getPosition()));
        $reader->moveForward(\strlen($match[0]));
        return \true;
    }
}
