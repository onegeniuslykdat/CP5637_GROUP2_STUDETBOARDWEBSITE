<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use Staatic\Vendor\Masterminds\HTML5\Elements;
class Tokenizer
{
    protected $scanner;
    protected $events;
    protected $tok;
    protected $text = '';
    protected $carryOn = \true;
    protected $textMode = 0;
    protected $untilTag = null;
    const CONFORMANT_XML = 'xml';
    const CONFORMANT_HTML = 'html';
    protected $mode = self::CONFORMANT_HTML;
    public function __construct($scanner, $eventHandler, $mode = self::CONFORMANT_HTML)
    {
        $this->scanner = $scanner;
        $this->events = $eventHandler;
        $this->mode = $mode;
    }
    public function parse()
    {
        do {
            $this->consumeData();
        } while ($this->carryOn);
    }
    public function setTextMode($textmode, $untilTag = null)
    {
        $this->textMode = $textmode & (Elements::TEXT_RAW | Elements::TEXT_RCDATA);
        $this->untilTag = $untilTag;
    }
    protected function consumeData()
    {
        $tok = $this->scanner->current();
        if ('&' === $tok) {
            $ref = $this->decodeCharacterReference();
            $this->buffer($ref);
            $tok = $this->scanner->current();
        }
        if ('<' === $tok) {
            $this->flushBuffer();
            $tok = $this->scanner->next();
            if (\false === $tok) {
                $this->parseError('Illegal tag opening');
            } elseif ('!' === $tok) {
                $this->markupDeclaration();
            } elseif ('/' === $tok) {
                $this->endTag();
            } elseif ('?' === $tok) {
                $this->processingInstruction();
            } elseif ($this->is_alpha($tok)) {
                $this->tagName();
            } else {
                $this->parseError('Illegal tag opening');
                $this->characterData();
            }
            $tok = $this->scanner->current();
        }
        if (\false === $tok) {
            $this->eof();
        } else {
            switch ($this->textMode) {
                case Elements::TEXT_RAW:
                    $this->rawText($tok);
                    break;
                case Elements::TEXT_RCDATA:
                    $this->rcdata($tok);
                    break;
                default:
                    if ('<' === $tok || '&' === $tok) {
                        break;
                    }
                    if ("\x00" === $tok) {
                        $this->parseError('Received null character.');
                        $this->text .= $tok;
                        $this->scanner->consume();
                        break;
                    }
                    $this->text .= $this->scanner->charsUntil("<&\x00");
            }
        }
        return $this->carryOn;
    }
    protected function characterData()
    {
        $tok = $this->scanner->current();
        if (\false === $tok) {
            return \false;
        }
        switch ($this->textMode) {
            case Elements::TEXT_RAW:
                return $this->rawText($tok);
            case Elements::TEXT_RCDATA:
                return $this->rcdata($tok);
            default:
                if ('<' === $tok || '&' === $tok) {
                    return \false;
                }
                return $this->text($tok);
        }
    }
    protected function text($tok)
    {
        if (\false === $tok) {
            return \false;
        }
        if ("\x00" === $tok) {
            $this->parseError('Received null character.');
        }
        $this->buffer($tok);
        $this->scanner->consume();
        return \true;
    }
    protected function rawText($tok)
    {
        if (is_null($this->untilTag)) {
            return $this->text($tok);
        }
        $sequence = '</' . $this->untilTag . '>';
        $txt = $this->readUntilSequence($sequence);
        $this->events->text($txt);
        $this->setTextMode(0);
        return $this->endTag();
    }
    protected function rcdata($tok)
    {
        if (is_null($this->untilTag)) {
            return $this->text($tok);
        }
        $sequence = '</' . $this->untilTag;
        $txt = '';
        $caseSensitive = !Elements::isHtml5Element($this->untilTag);
        while (\false !== $tok && !('<' == $tok && $this->scanner->sequenceMatches($sequence, $caseSensitive))) {
            if ('&' == $tok) {
                $txt .= $this->decodeCharacterReference();
                $tok = $this->scanner->current();
            } else {
                $txt .= $tok;
                $tok = $this->scanner->next();
            }
        }
        $len = strlen($sequence);
        $this->scanner->consume($len);
        $len += $this->scanner->whitespace();
        if ('>' !== $this->scanner->current()) {
            $this->parseError('Unclosed RCDATA end tag');
        }
        $this->scanner->unconsume($len);
        $this->events->text($txt);
        $this->setTextMode(0);
        return $this->endTag();
    }
    protected function eof()
    {
        $this->flushBuffer();
        $this->events->eof();
        $this->carryOn = \false;
    }
    protected function markupDeclaration()
    {
        $tok = $this->scanner->next();
        if ('-' == $tok && '-' == $this->scanner->peek()) {
            $this->scanner->consume(2);
            return $this->comment();
        } elseif ('D' == $tok || 'd' == $tok) {
            return $this->doctype();
        } elseif ('[' == $tok) {
            return $this->cdataSection();
        }
        $this->parseError('Expected <!--, <![CDATA[, or <!DOCTYPE. Got <!%s', $tok);
        $this->bogusComment('<!');
        return \true;
    }
    protected function endTag()
    {
        if ('/' != $this->scanner->current()) {
            return \false;
        }
        $tok = $this->scanner->next();
        if (!$this->is_alpha($tok)) {
            $this->parseError("Expected tag name, got '%s'", $tok);
            if ("\x00" == $tok || \false === $tok) {
                return \false;
            }
            return $this->bogusComment('</');
        }
        $name = $this->scanner->charsUntil("\n\f \t>");
        $name = (self::CONFORMANT_XML === $this->mode) ? $name : strtolower($name);
        $this->scanner->whitespace();
        $tok = $this->scanner->current();
        if ('>' != $tok) {
            $this->parseError("Expected >, got '%s'", $tok);
            $this->scanner->charsUntil('>');
        }
        $this->events->endTag($name);
        $this->scanner->consume();
        return \true;
    }
    protected function tagName()
    {
        $name = $this->scanner->charsWhile(':_-0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        $name = (self::CONFORMANT_XML === $this->mode) ? $name : strtolower($name);
        $attributes = array();
        $selfClose = \false;
        try {
            do {
                $this->scanner->whitespace();
                $this->attribute($attributes);
            } while (!$this->isTagEnd($selfClose));
        } catch (ParseError $e) {
            $selfClose = \false;
        }
        $mode = $this->events->startTag($name, $attributes, $selfClose);
        if (is_int($mode)) {
            $this->setTextMode($mode, $name);
        }
        $this->scanner->consume();
        return \true;
    }
    protected function isTagEnd(&$selfClose)
    {
        $tok = $this->scanner->current();
        if ('/' == $tok) {
            $this->scanner->consume();
            $this->scanner->whitespace();
            $tok = $this->scanner->current();
            if ('>' == $tok) {
                $selfClose = \true;
                return \true;
            }
            if (\false === $tok) {
                $this->parseError('Unexpected EOF inside of tag.');
                return \true;
            }
            $this->parseError("Unexpected '%s' inside of a tag.", $tok);
            return \false;
        }
        if ('>' == $tok) {
            return \true;
        }
        if (\false === $tok) {
            $this->parseError('Unexpected EOF inside of tag.');
            return \true;
        }
        return \false;
    }
    protected function attribute(&$attributes)
    {
        $tok = $this->scanner->current();
        if ('/' == $tok || '>' == $tok || \false === $tok) {
            return \false;
        }
        if ('<' == $tok) {
            $this->parseError("Unexpected '<' inside of attributes list.");
            $this->scanner->unconsume();
            throw new ParseError('Start tag inside of attribute.');
        }
        $name = strtolower($this->scanner->charsUntil("/>=\n\f\t "));
        if (0 == strlen($name)) {
            $tok = $this->scanner->current();
            $this->parseError('Expected an attribute name, got %s.', $tok);
            $name = $tok;
            $this->scanner->consume();
        }
        $isValidAttribute = \true;
        if (preg_match("/[\x01-,\\/;-@[-^`{-]/u", $name)) {
            $this->parseError('Unexpected characters in attribute name: %s', $name);
            $isValidAttribute = \false;
        } elseif (preg_match('/^[0-9.-]/u', $name)) {
            $this->parseError('Unexpected character at the begining of attribute name: %s', $name);
            $isValidAttribute = \false;
        }
        $this->scanner->whitespace();
        $val = $this->attributeValue();
        if ($isValidAttribute) {
            $attributes[$name] = $val;
        }
        return \true;
    }
    protected function attributeValue()
    {
        if ('=' != $this->scanner->current()) {
            return null;
        }
        $this->scanner->consume();
        $this->scanner->whitespace();
        $tok = $this->scanner->current();
        switch ($tok) {
            case "\n":
            case "\f":
            case ' ':
            case "\t":
                return null;
            case '"':
            case "'":
                $this->scanner->consume();
                return $this->quotedAttributeValue($tok);
            case '>':
                $this->parseError('Expected attribute value, got tag end.');
                return null;
            case '=':
            case '`':
                $this->parseError('Expecting quotes, got %s.', $tok);
                return $this->unquotedAttributeValue();
            default:
                return $this->unquotedAttributeValue();
        }
    }
    protected function quotedAttributeValue($quote)
    {
        $stoplist = "\f" . $quote;
        $val = '';
        while (\true) {
            $tokens = $this->scanner->charsUntil($stoplist . '&');
            if (\false !== $tokens) {
                $val .= $tokens;
            } else {
                break;
            }
            $tok = $this->scanner->current();
            if ('&' == $tok) {
                $val .= $this->decodeCharacterReference(\true);
                continue;
            }
            break;
        }
        $this->scanner->consume();
        return $val;
    }
    protected function unquotedAttributeValue()
    {
        $val = '';
        $tok = $this->scanner->current();
        while (\false !== $tok) {
            switch ($tok) {
                case "\n":
                case "\f":
                case ' ':
                case "\t":
                case '>':
                    break 2;
                case '&':
                    $val .= $this->decodeCharacterReference(\true);
                    $tok = $this->scanner->current();
                    break;
                case "'":
                case '"':
                case '<':
                case '=':
                case '`':
                    $this->parseError('Unexpected chars in unquoted attribute value %s', $tok);
                    $val .= $tok;
                    $tok = $this->scanner->next();
                    break;
                default:
                    $val .= $this->scanner->charsUntil("\t\n\f >&\"'<=`");
                    $tok = $this->scanner->current();
            }
        }
        return $val;
    }
    protected function bogusComment($leading = '')
    {
        $comment = $leading;
        $tokens = $this->scanner->charsUntil('>');
        if (\false !== $tokens) {
            $comment .= $tokens;
        }
        $tok = $this->scanner->current();
        if (\false !== $tok) {
            $comment .= $tok;
        }
        $this->flushBuffer();
        $this->events->comment($comment);
        $this->scanner->consume();
        return \true;
    }
    protected function comment()
    {
        $tok = $this->scanner->current();
        $comment = '';
        if ('>' == $tok) {
            $this->parseError("Expected comment data, got '>'");
            $this->events->comment('');
            $this->scanner->consume();
            return \true;
        }
        if ("\x00" == $tok) {
            $tok = UTF8Utils::FFFD;
        }
        while (!$this->isCommentEnd()) {
            $comment .= $tok;
            $tok = $this->scanner->next();
        }
        $this->events->comment($comment);
        $this->scanner->consume();
        return \true;
    }
    protected function isCommentEnd()
    {
        $tok = $this->scanner->current();
        if (\false === $tok) {
            $this->parseError('Unexpected EOF in a comment.');
            return \true;
        }
        if ('-' != $tok || '-' != $this->scanner->peek()) {
            return \false;
        }
        $this->scanner->consume(2);
        if ('>' == $this->scanner->current()) {
            return \true;
        }
        if ('!' == $this->scanner->current() && '>' == $this->scanner->peek()) {
            $this->scanner->consume();
            return \true;
        }
        $this->scanner->unconsume(2);
        return \false;
    }
    protected function doctype()
    {
        if ($this->scanner->sequenceMatches('DOCTYPE', \false)) {
            $this->scanner->consume(7);
        } else {
            $chars = $this->scanner->charsWhile('DOCTYPEdoctype');
            $this->parseError('Expected DOCTYPE, got %s', $chars);
            return $this->bogusComment('<!' . $chars);
        }
        $this->scanner->whitespace();
        $tok = $this->scanner->current();
        if (\false === $tok) {
            $this->events->doctype('html5', EventHandler::DOCTYPE_NONE, '', \true);
            $this->eof();
            return \true;
        }
        if ("\x00" === $tok) {
            $this->parseError('Unexpected null character in DOCTYPE.');
        }
        $stop = " \n\f>";
        $doctypeName = $this->scanner->charsUntil($stop);
        $doctypeName = strtolower(strtr($doctypeName, "\x00", UTF8Utils::FFFD));
        $tok = $this->scanner->current();
        if (\false === $tok) {
            $this->parseError('Unexpected EOF in DOCTYPE declaration.');
            $this->events->doctype($doctypeName, EventHandler::DOCTYPE_NONE, null, \true);
            return \true;
        }
        if ('>' == $tok) {
            if (0 == strlen($doctypeName)) {
                $this->parseError('Expected a DOCTYPE name. Got nothing.');
                $this->events->doctype($doctypeName, 0, null, \true);
                $this->scanner->consume();
                return \true;
            }
            $this->events->doctype($doctypeName);
            $this->scanner->consume();
            return \true;
        }
        $this->scanner->whitespace();
        $pub = strtoupper($this->scanner->getAsciiAlpha());
        $white = $this->scanner->whitespace();
        if (('PUBLIC' == $pub || 'SYSTEM' == $pub) && $white > 0) {
            $type = ('PUBLIC' == $pub) ? EventHandler::DOCTYPE_PUBLIC : EventHandler::DOCTYPE_SYSTEM;
            $id = $this->quotedString("\x00>");
            if (\false === $id) {
                $this->events->doctype($doctypeName, $type, $pub, \false);
                return \true;
            }
            if (\false === $this->scanner->current()) {
                $this->parseError('Unexpected EOF in DOCTYPE');
                $this->events->doctype($doctypeName, $type, $id, \true);
                return \true;
            }
            $this->scanner->whitespace();
            if ('>' == $this->scanner->current()) {
                $this->events->doctype($doctypeName, $type, $id, \false);
                $this->scanner->consume();
                return \true;
            }
            $this->scanner->charsUntil('>');
            $this->parseError('Malformed DOCTYPE.');
            $this->events->doctype($doctypeName, $type, $id, \true);
            $this->scanner->consume();
            return \true;
        }
        $this->scanner->charsUntil('>');
        $this->parseError('Expected PUBLIC or SYSTEM. Got %s.', $pub);
        $this->events->doctype($doctypeName, 0, null, \true);
        $this->scanner->consume();
        return \true;
    }
    protected function quotedString($stopchars)
    {
        $tok = $this->scanner->current();
        if ('"' == $tok || "'" == $tok) {
            $this->scanner->consume();
            $ret = $this->scanner->charsUntil($tok . $stopchars);
            if ($this->scanner->current() == $tok) {
                $this->scanner->consume();
            } else {
                $this->parseError('Expected %s, got %s', $tok, $this->scanner->current());
            }
            return $ret;
        }
        return \false;
    }
    protected function cdataSection()
    {
        $cdata = '';
        $this->scanner->consume();
        $chars = $this->scanner->charsWhile('CDAT');
        if ('CDATA' != $chars || '[' != $this->scanner->current()) {
            $this->parseError('Expected [CDATA[, got %s', $chars);
            return $this->bogusComment('<![' . $chars);
        }
        $tok = $this->scanner->next();
        do {
            if (\false === $tok) {
                $this->parseError('Unexpected EOF inside CDATA.');
                $this->bogusComment('<![CDATA[' . $cdata);
                return \true;
            }
            $cdata .= $tok;
            $tok = $this->scanner->next();
        } while (!$this->scanner->sequenceMatches(']]>'));
        $this->scanner->consume(3);
        $this->events->cdata($cdata);
        return \true;
    }
    protected function processingInstruction()
    {
        if ('?' != $this->scanner->current()) {
            return \false;
        }
        $tok = $this->scanner->next();
        $procName = $this->scanner->getAsciiAlpha();
        $white = $this->scanner->whitespace();
        if (0 == strlen($procName) || 0 == $white || \false == $this->scanner->current()) {
            $this->parseError("Expected processing instruction name, got {$tok}");
            $this->bogusComment('<?' . $tok . $procName);
            return \true;
        }
        $data = '';
        while (!('?' == $this->scanner->current() && '>' == $this->scanner->peek())) {
            $data .= $this->scanner->current();
            $tok = $this->scanner->next();
            if (\false === $tok) {
                $this->parseError('Unexpected EOF in processing instruction.');
                $this->events->processingInstruction($procName, $data);
                return \true;
            }
        }
        $this->scanner->consume(2);
        $this->events->processingInstruction($procName, $data);
        return \true;
    }
    protected function readUntilSequence($sequence)
    {
        $buffer = '';
        $first = substr($sequence, 0, 1);
        while (\false !== $this->scanner->current()) {
            $buffer .= $this->scanner->charsUntil($first);
            if ($this->scanner->sequenceMatches($sequence, \false)) {
                return $buffer;
            }
            $buffer .= $this->scanner->current();
            $this->scanner->consume();
        }
        $this->parseError('Unexpected EOF during text read.');
        return $buffer;
    }
    protected function sequenceMatches($sequence, $caseSensitive = \true)
    {
        @trigger_error(__METHOD__ . ' method is deprecated since version 2.4 and will be removed in 3.0. Use Scanner::sequenceMatches() instead.', \E_USER_DEPRECATED);
        return $this->scanner->sequenceMatches($sequence, $caseSensitive);
    }
    protected function flushBuffer()
    {
        if ('' === $this->text) {
            return;
        }
        $this->events->text($this->text);
        $this->text = '';
    }
    protected function buffer($str)
    {
        $this->text .= $str;
    }
    protected function parseError($msg)
    {
        $args = func_get_args();
        if (count($args) > 1) {
            array_shift($args);
            $msg = vsprintf($msg, $args);
        }
        $line = $this->scanner->currentLine();
        $col = $this->scanner->columnOffset();
        $this->events->parseError($msg, $line, $col);
        return \false;
    }
    protected function decodeCharacterReference($inAttribute = \false)
    {
        $tok = $this->scanner->next();
        $start = $this->scanner->position();
        if (\false === $tok) {
            return '&';
        }
        if ("\t" === $tok || "\n" === $tok || "\f" === $tok || ' ' === $tok || '&' === $tok || '<' === $tok) {
            return '&';
        }
        if ('#' === $tok) {
            $tok = $this->scanner->next();
            if (\false === $tok) {
                $this->parseError('Expected &#DEC; &#HEX;, got EOF');
                $this->scanner->unconsume(1);
                return '&';
            }
            if ('x' === $tok || 'X' === $tok) {
                $tok = $this->scanner->next();
                $hex = $this->scanner->getHex();
                if (empty($hex)) {
                    $this->parseError('Expected &#xHEX;, got &#x%s', $tok);
                    $this->scanner->unconsume(2);
                    return '&';
                }
                $entity = CharacterReference::lookupHex($hex);
            } else {
                $numeric = $this->scanner->getNumeric();
                if (\false === $numeric) {
                    $this->parseError('Expected &#DIGITS;, got &#%s', $tok);
                    $this->scanner->unconsume(2);
                    return '&';
                }
                $entity = CharacterReference::lookupDecimal($numeric);
            }
        } elseif ('=' === $tok && $inAttribute) {
            return '&';
        } else {
            $cname = $this->scanner->getAsciiAlphaNum();
            $entity = CharacterReference::lookupName($cname);
            if (null === $entity) {
                if (!$inAttribute || '' === $cname) {
                    $this->parseError("No match in entity table for '%s'", $cname);
                }
                $this->scanner->unconsume($this->scanner->position() - $start);
                return '&';
            }
        }
        $tok = $this->scanner->current();
        if (';' === $tok) {
            $this->scanner->consume();
            return $entity;
        }
        $this->scanner->unconsume($this->scanner->position() - $start);
        $this->parseError('Expected &ENTITY;, got &ENTITY%s (no trailing ;) ', $tok);
        return '&';
    }
    protected function is_alpha($input)
    {
        $code = ord($input);
        return $code >= 97 && $code <= 122 || $code >= 65 && $code <= 90;
    }
}
