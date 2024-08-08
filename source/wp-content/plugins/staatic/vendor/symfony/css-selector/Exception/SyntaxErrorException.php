<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Exception;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Token;
class SyntaxErrorException extends ParseException
{
    /**
     * @param string $expectedValue
     * @param Token $foundToken
     */
    public static function unexpectedToken($expectedValue, $foundToken): self
    {
        return new self(sprintf('Expected %s, but %s found.', $expectedValue, $foundToken));
    }
    /**
     * @param string $pseudoElement
     * @param string $unexpectedLocation
     */
    public static function pseudoElementFound($pseudoElement, $unexpectedLocation): self
    {
        return new self(sprintf('Unexpected pseudo-element "::%s" found %s.', $pseudoElement, $unexpectedLocation));
    }
    /**
     * @param int $position
     */
    public static function unclosedString($position): self
    {
        return new self(sprintf('Unclosed/invalid string at %s.', $position));
    }
    public static function nestedNot(): self
    {
        return new self('Got nested ::not().');
    }
    /**
     * @param string $pseudoElement
     */
    public static function notAtTheStartOfASelector($pseudoElement): self
    {
        return new self(sprintf('Got immediate child pseudo-element ":%s" not at the start of a selector', $pseudoElement));
    }
    public static function stringAsFunctionArgument(): self
    {
        return new self('String not allowed as function argument.');
    }
}
