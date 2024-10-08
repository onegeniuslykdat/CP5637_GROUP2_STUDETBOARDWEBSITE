<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer;

class TokenizerPatterns
{
    /**
     * @var string
     */
    private $unicodeEscapePattern;
    /**
     * @var string
     */
    private $simpleEscapePattern;
    /**
     * @var string
     */
    private $newLineEscapePattern;
    /**
     * @var string
     */
    private $escapePattern;
    /**
     * @var string
     */
    private $stringEscapePattern;
    /**
     * @var string
     */
    private $nonAsciiPattern;
    /**
     * @var string
     */
    private $nmCharPattern;
    /**
     * @var string
     */
    private $nmStartPattern;
    /**
     * @var string
     */
    private $identifierPattern;
    /**
     * @var string
     */
    private $hashPattern;
    /**
     * @var string
     */
    private $numberPattern;
    /**
     * @var string
     */
    private $quotedStringPattern;
    public function __construct()
    {
        $this->unicodeEscapePattern = '\\\\([0-9a-f]{1,6})(?:\r\n|[ \n\r\t\f])?';
        $this->simpleEscapePattern = '\\\\(.)';
        $this->newLineEscapePattern = '\\\\(?:\n|\r\n|\r|\f)';
        $this->escapePattern = $this->unicodeEscapePattern . '|\\\\[^\n\r\f0-9a-f]';
        $this->stringEscapePattern = $this->newLineEscapePattern . '|' . $this->escapePattern;
        $this->nonAsciiPattern = '[^\x00-\x7F]';
        $this->nmCharPattern = '[_a-z0-9-]|' . $this->escapePattern . '|' . $this->nonAsciiPattern;
        $this->nmStartPattern = '[_a-z]|' . $this->escapePattern . '|' . $this->nonAsciiPattern;
        $this->identifierPattern = '-?(?:' . $this->nmStartPattern . ')(?:' . $this->nmCharPattern . ')*';
        $this->hashPattern = '#((?:' . $this->nmCharPattern . ')+)';
        $this->numberPattern = '[+-]?(?:[0-9]*\.[0-9]+|[0-9]+)';
        $this->quotedStringPattern = '([^\n\r\f\\\\%s]|' . $this->stringEscapePattern . ')*';
    }
    public function getNewLineEscapePattern(): string
    {
        return '~' . $this->newLineEscapePattern . '~';
    }
    public function getSimpleEscapePattern(): string
    {
        return '~' . $this->simpleEscapePattern . '~';
    }
    public function getUnicodeEscapePattern(): string
    {
        return '~' . $this->unicodeEscapePattern . '~i';
    }
    public function getIdentifierPattern(): string
    {
        return '~^' . $this->identifierPattern . '~i';
    }
    public function getHashPattern(): string
    {
        return '~^' . $this->hashPattern . '~i';
    }
    public function getNumberPattern(): string
    {
        return '~^' . $this->numberPattern . '~';
    }
    /**
     * @param string $quote
     */
    public function getQuotedStringPattern($quote): string
    {
        return '~^' . sprintf($this->quotedStringPattern, $quote) . '~i';
    }
}
