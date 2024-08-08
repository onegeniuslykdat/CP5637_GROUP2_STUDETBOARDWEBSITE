<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\CombinedSelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\HashNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\ClassNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\PseudoNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\NegationNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\FunctionNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\ElementNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\NodeInterface;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\AttributeNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Node;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Tokenizer\Tokenizer;
class Parser implements ParserInterface
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;
    public function __construct(?Tokenizer $tokenizer = null)
    {
        $this->tokenizer = $tokenizer ?? new Tokenizer();
    }
    /**
     * @param string $source
     */
    public function parse($source): array
    {
        $reader = new Reader($source);
        $stream = $this->tokenizer->tokenize($reader);
        return $this->parseSelectorList($stream);
    }
    /**
     * @param mixed[] $tokens
     */
    public static function parseSeries($tokens): array
    {
        foreach ($tokens as $token) {
            if ($token->isString()) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }
        }
        $joined = trim(implode('', array_map(function (Token $token) {
            return $token->getValue();
        }, $tokens)));
        $int = function ($string) {
            if (!is_numeric($string)) {
                throw SyntaxErrorException::stringAsFunctionArgument();
            }
            return (int) $string;
        };
        switch (\true) {
            case 'odd' === $joined:
                return [2, 1];
            case 'even' === $joined:
                return [2, 0];
            case 'n' === $joined:
                return [1, 0];
            case strpos($joined, 'n') === false:
                return [0, $int($joined)];
        }
        $split = explode('n', $joined);
        $first = $split[0] ?? null;
        return [$first ? ('-' === $first || '+' === $first) ? $int($first . '1') : $int($first) : 1, (isset($split[1]) && $split[1]) ? $int($split[1]) : 0];
    }
    private function parseSelectorList(TokenStream $stream): array
    {
        $stream->skipWhitespace();
        $selectors = [];
        while (\true) {
            $selectors[] = $this->parserSelectorNode($stream);
            if ($stream->getPeek()->isDelimiter([','])) {
                $stream->getNext();
                $stream->skipWhitespace();
            } else {
                break;
            }
        }
        return $selectors;
    }
    private function parserSelectorNode(TokenStream $stream): SelectorNode
    {
        [$result, $pseudoElement] = $this->parseSimpleSelector($stream);
        while (\true) {
            $stream->skipWhitespace();
            $peek = $stream->getPeek();
            if ($peek->isFileEnd() || $peek->isDelimiter([','])) {
                break;
            }
            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }
            if ($peek->isDelimiter(['+', '>', '~'])) {
                $combinator = $stream->getNext()->getValue();
                $stream->skipWhitespace();
            } else {
                $combinator = ' ';
            }
            [$nextSelector, $pseudoElement] = $this->parseSimpleSelector($stream);
            $result = new CombinedSelectorNode($result, $combinator, $nextSelector);
        }
        return new SelectorNode($result, $pseudoElement);
    }
    private function parseSimpleSelector(TokenStream $stream, bool $insideNegation = \false): array
    {
        $stream->skipWhitespace();
        $selectorStart = \count($stream->getUsed());
        $result = $this->parseElementNode($stream);
        $pseudoElement = null;
        while (\true) {
            $peek = $stream->getPeek();
            if ($peek->isWhitespace() || $peek->isFileEnd() || $peek->isDelimiter([',', '+', '>', '~']) || $insideNegation && $peek->isDelimiter([')'])) {
                break;
            }
            if (null !== $pseudoElement) {
                throw SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }
            if ($peek->isHash()) {
                $result = new HashNode($result, $stream->getNext()->getValue());
            } elseif ($peek->isDelimiter(['.'])) {
                $stream->getNext();
                $result = new ClassNode($result, $stream->getNextIdentifier());
            } elseif ($peek->isDelimiter(['['])) {
                $stream->getNext();
                $result = $this->parseAttributeNode($result, $stream);
            } elseif ($peek->isDelimiter([':'])) {
                $stream->getNext();
                if ($stream->getPeek()->isDelimiter([':'])) {
                    $stream->getNext();
                    $pseudoElement = $stream->getNextIdentifier();
                    continue;
                }
                $identifier = $stream->getNextIdentifier();
                if (\in_array(strtolower($identifier), ['first-line', 'first-letter', 'before', 'after'])) {
                    $pseudoElement = $identifier;
                    continue;
                }
                if (!$stream->getPeek()->isDelimiter(['('])) {
                    $result = new PseudoNode($result, $identifier);
                    if ('Pseudo[Element[*]:scope]' === $result->__toString()) {
                        $used = \count($stream->getUsed());
                        if (!(2 === $used || 3 === $used && $stream->getUsed()[0]->isWhiteSpace() || $used >= 3 && $stream->getUsed()[$used - 3]->isDelimiter([',']) || $used >= 4 && $stream->getUsed()[$used - 3]->isWhiteSpace() && $stream->getUsed()[$used - 4]->isDelimiter([',']))) {
                            throw SyntaxErrorException::notAtTheStartOfASelector('scope');
                        }
                    }
                    continue;
                }
                $stream->getNext();
                $stream->skipWhitespace();
                if ('not' === strtolower($identifier)) {
                    if ($insideNegation) {
                        throw SyntaxErrorException::nestedNot();
                    }
                    [$argument, $argumentPseudoElement] = $this->parseSimpleSelector($stream, \true);
                    $next = $stream->getNext();
                    if (null !== $argumentPseudoElement) {
                        throw SyntaxErrorException::pseudoElementFound($argumentPseudoElement, 'inside ::not()');
                    }
                    if (!$next->isDelimiter([')'])) {
                        throw SyntaxErrorException::unexpectedToken('")"', $next);
                    }
                    $result = new NegationNode($result, $argument);
                } else {
                    $arguments = [];
                    $next = null;
                    while (\true) {
                        $stream->skipWhitespace();
                        $next = $stream->getNext();
                        if ($next->isIdentifier() || $next->isString() || $next->isNumber() || $next->isDelimiter(['+', '-'])) {
                            $arguments[] = $next;
                        } elseif ($next->isDelimiter([')'])) {
                            break;
                        } else {
                            throw SyntaxErrorException::unexpectedToken('an argument', $next);
                        }
                    }
                    if (!$arguments) {
                        throw SyntaxErrorException::unexpectedToken('at least one argument', $next);
                    }
                    $result = new FunctionNode($result, $identifier, $arguments);
                }
            } else {
                throw SyntaxErrorException::unexpectedToken('selector', $peek);
            }
        }
        if (\count($stream->getUsed()) === $selectorStart) {
            throw SyntaxErrorException::unexpectedToken('selector', $stream->getPeek());
        }
        return [$result, $pseudoElement];
    }
    private function parseElementNode(TokenStream $stream): ElementNode
    {
        $peek = $stream->getPeek();
        if ($peek->isIdentifier() || $peek->isDelimiter(['*'])) {
            if ($peek->isIdentifier()) {
                $namespace = $stream->getNext()->getValue();
            } else {
                $stream->getNext();
                $namespace = null;
            }
            if ($stream->getPeek()->isDelimiter(['|'])) {
                $stream->getNext();
                $element = $stream->getNextIdentifierOrStar();
            } else {
                $element = $namespace;
                $namespace = null;
            }
        } else {
            $element = $namespace = null;
        }
        return new ElementNode($namespace, $element);
    }
    private function parseAttributeNode(NodeInterface $selector, TokenStream $stream): AttributeNode
    {
        $stream->skipWhitespace();
        $attribute = $stream->getNextIdentifierOrStar();
        if (null === $attribute && !$stream->getPeek()->isDelimiter(['|'])) {
            throw SyntaxErrorException::unexpectedToken('"|"', $stream->getPeek());
        }
        if ($stream->getPeek()->isDelimiter(['|'])) {
            $stream->getNext();
            if ($stream->getPeek()->isDelimiter(['='])) {
                $namespace = null;
                $stream->getNext();
                $operator = '|=';
            } else {
                $namespace = $attribute;
                $attribute = $stream->getNextIdentifier();
                $operator = null;
            }
        } else {
            $namespace = $operator = null;
        }
        if (null === $operator) {
            $stream->skipWhitespace();
            $next = $stream->getNext();
            if ($next->isDelimiter([']'])) {
                return new AttributeNode($selector, $namespace, $attribute, 'exists', null);
            } elseif ($next->isDelimiter(['='])) {
                $operator = '=';
            } elseif ($next->isDelimiter(['^', '$', '*', '~', '|', '!']) && $stream->getPeek()->isDelimiter(['='])) {
                $operator = $next->getValue() . '=';
                $stream->getNext();
            } else {
                throw SyntaxErrorException::unexpectedToken('operator', $next);
            }
        }
        $stream->skipWhitespace();
        $value = $stream->getNext();
        if ($value->isNumber()) {
            $value = new Token(Token::TYPE_STRING, (string) $value->getValue(), $value->getPosition());
        }
        if (!($value->isIdentifier() || $value->isString())) {
            throw SyntaxErrorException::unexpectedToken('string or identifier', $value);
        }
        $stream->skipWhitespace();
        $next = $stream->getNext();
        if (!$next->isDelimiter([']'])) {
            throw SyntaxErrorException::unexpectedToken('"]"', $next);
        }
        return new AttributeNode($selector, $namespace, $attribute, $operator, $value->getValue());
    }
}
