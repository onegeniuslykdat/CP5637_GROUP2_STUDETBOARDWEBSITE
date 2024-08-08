<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\WhitespaceHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\IdentifierHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\HashHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\StringHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\NumberHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler\CommentHandler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Handler;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Reader;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\TokenStream;
class Tokenizer
{
    /**
     * @var mixed[]
     */
    private $handlers;
    public function __construct()
    {
        $patterns = new TokenizerPatterns();
        $escaping = new TokenizerEscaping($patterns);
        $this->handlers = [new WhitespaceHandler(), new IdentifierHandler($patterns, $escaping), new HashHandler($patterns, $escaping), new StringHandler($patterns, $escaping), new NumberHandler($patterns), new CommentHandler()];
    }
    /**
     * @param Reader $reader
     */
    public function tokenize($reader): TokenStream
    {
        $stream = new TokenStream();
        while (!$reader->isEOF()) {
            foreach ($this->handlers as $handler) {
                if ($handler->handle($reader, $stream)) {
                    continue 2;
                }
            }
            $stream->push(new Token(Token::TYPE_DELIMITER, $reader->getSubstring(1), $reader->getPosition()));
            $reader->moveForward(1);
        }
        return $stream->push(new Token(Token::TYPE_FILE_END, null, $reader->getPosition()))->freeze();
    }
}
