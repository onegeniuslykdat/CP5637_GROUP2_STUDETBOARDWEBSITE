<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use Staatic\Vendor\Masterminds\HTML5\Exception;
class Scanner
{
    const CHARS_HEX = 'abcdefABCDEF01234567890';
    const CHARS_ALNUM = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
    const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $data;
    private $char;
    private $EOF;
    public $errors = array();
    public function __construct($data, $encoding = 'UTF-8')
    {
        if ($data instanceof InputStream) {
            @trigger_error('InputStream objects are deprecated since version 2.4 and will be removed in 3.0. Use strings instead.', \E_USER_DEPRECATED);
            $data = (string) $data;
        }
        $data = UTF8Utils::convertToUTF8($data, $encoding);
        $this->errors = UTF8Utils::checkForIllegalCodepoints($data);
        $data = $this->replaceLinefeeds($data);
        $this->data = $data;
        $this->char = 0;
        $this->EOF = strlen($data);
    }
    public function sequenceMatches($sequence, $caseSensitive = \true)
    {
        $portion = substr($this->data, $this->char, strlen($sequence));
        return $caseSensitive ? $portion === $sequence : (0 === strcasecmp($portion, $sequence));
    }
    public function position()
    {
        return $this->char;
    }
    public function peek()
    {
        if ($this->char + 1 < $this->EOF) {
            return $this->data[$this->char + 1];
        }
        return \false;
    }
    public function next()
    {
        ++$this->char;
        if ($this->char < $this->EOF) {
            return $this->data[$this->char];
        }
        return \false;
    }
    public function current()
    {
        if ($this->char < $this->EOF) {
            return $this->data[$this->char];
        }
        return \false;
    }
    public function consume($count = 1)
    {
        $this->char += $count;
    }
    public function unconsume($howMany = 1)
    {
        if ($this->char - $howMany >= 0) {
            $this->char -= $howMany;
        }
    }
    public function getHex()
    {
        return $this->doCharsWhile(static::CHARS_HEX);
    }
    public function getAsciiAlpha()
    {
        return $this->doCharsWhile(static::CHARS_ALPHA);
    }
    public function getAsciiAlphaNum()
    {
        return $this->doCharsWhile(static::CHARS_ALNUM);
    }
    public function getNumeric()
    {
        return $this->doCharsWhile('0123456789');
    }
    public function whitespace()
    {
        if ($this->char >= $this->EOF) {
            return \false;
        }
        $len = strspn($this->data, "\n\t\f ", $this->char);
        $this->char += $len;
        return $len;
    }
    public function currentLine()
    {
        if (empty($this->EOF) || 0 === $this->char) {
            return 1;
        }
        return substr_count($this->data, "\n", 0, min($this->char, $this->EOF)) + 1;
    }
    public function charsUntil($mask)
    {
        return $this->doCharsUntil($mask);
    }
    public function charsWhile($mask)
    {
        return $this->doCharsWhile($mask);
    }
    public function columnOffset()
    {
        if (0 === $this->char) {
            return 0;
        }
        $backwardFrom = $this->char - 1 - strlen($this->data);
        $lastLine = strrpos($this->data, "\n", $backwardFrom);
        if (\false !== $lastLine) {
            $findLengthOf = substr($this->data, $lastLine + 1, $this->char - 1 - $lastLine);
        } else {
            $findLengthOf = substr($this->data, 0, $this->char);
        }
        return UTF8Utils::countChars($findLengthOf);
    }
    public function remainingChars()
    {
        if ($this->char < $this->EOF) {
            $data = substr($this->data, $this->char);
            $this->char = $this->EOF;
            return $data;
        }
        return '';
    }
    private function replaceLinefeeds($data)
    {
        $crlfTable = array("\x00" => "�", "\r\n" => "\n", "\r" => "\n");
        return strtr($data, $crlfTable);
    }
    private function doCharsUntil($bytes, $max = null)
    {
        if ($this->char >= $this->EOF) {
            return \false;
        }
        if (0 === $max || $max) {
            $len = strcspn($this->data, $bytes, $this->char, $max);
        } else {
            $len = strcspn($this->data, $bytes, $this->char);
        }
        $string = (string) substr($this->data, $this->char, $len);
        $this->char += $len;
        return $string;
    }
    private function doCharsWhile($bytes, $max = null)
    {
        if ($this->char >= $this->EOF) {
            return \false;
        }
        if (0 === $max || $max) {
            $len = strspn($this->data, $bytes, $this->char, $max);
        } else {
            $len = strspn($this->data, $bytes, $this->char);
        }
        $string = (string) substr($this->data, $this->char, $len);
        $this->char += $len;
        return $string;
    }
}
