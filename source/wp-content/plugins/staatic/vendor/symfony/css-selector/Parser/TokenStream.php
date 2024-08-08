<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

use Staatic\Vendor\Symfony\Component\CssSelector\Exception\InternalErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException;
class TokenStream
{
    /**
     * @var mixed[]
     */
    private $tokens = [];
    /**
     * @var mixed[]
     */
    private $used = [];
    /**
     * @var int
     */
    private $cursor = 0;
    /**
     * @var Token|null
     */
    private $peeked;
    /**
     * @var bool
     */
    private $peeking = \false;
    /**
     * @param Token $token
     * @return static
     */
    public function push($token)
    {
        $this->tokens[] = $token;
        return $this;
    }
    /**
     * @return static
     */
    public function freeze()
    {
        return $this;
    }
    public function getNext(): Token
    {
        if ($this->peeking) {
            $this->peeking = \false;
            $this->used[] = $this->peeked;
            return $this->peeked;
        }
        if (!isset($this->tokens[$this->cursor])) {
            throw new InternalErrorException('Unexpected token stream end.');
        }
        return $this->tokens[$this->cursor++];
    }
    public function getPeek(): Token
    {
        if (!$this->peeking) {
            $this->peeked = $this->getNext();
            $this->peeking = \true;
        }
        return $this->peeked;
    }
    public function getUsed(): array
    {
        return $this->used;
    }
    public function getNextIdentifier(): string
    {
        $next = $this->getNext();
        if (!$next->isIdentifier()) {
            throw SyntaxErrorException::unexpectedToken('identifier', $next);
        }
        return $next->getValue();
    }
    public function getNextIdentifierOrStar(): ?string
    {
        $next = $this->getNext();
        if ($next->isIdentifier()) {
            return $next->getValue();
        }
        if ($next->isDelimiter(['*'])) {
            return null;
        }
        throw SyntaxErrorException::unexpectedToken('identifier or "*"', $next);
    }
    public function skipWhitespace(): void
    {
        $peek = $this->getPeek();
        if ($peek->isWhitespace()) {
            $this->getNext();
        }
    }
}
