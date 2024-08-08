<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use ReturnTypeWillChange;
class StringInputStream implements InputStream
{
    private $data;
    private $char;
    private $EOF;
    public $errors = array();
    public function __construct($data, $encoding = 'UTF-8', $debug = '')
    {
        $data = UTF8Utils::convertToUTF8($data, $encoding);
        if ($debug) {
            fprintf(\STDOUT, $debug, $data, strlen($data));
        }
        $this->errors = UTF8Utils::checkForIllegalCodepoints($data);
        $data = $this->replaceLinefeeds($data);
        $this->data = $data;
        $this->char = 0;
        $this->EOF = strlen($data);
    }
    public function __toString()
    {
        return $this->data;
    }
    protected function replaceLinefeeds($data)
    {
        $crlfTable = array("\x00" => "�", "\r\n" => "\n", "\r" => "\n");
        return strtr($data, $crlfTable);
    }
    public function currentLine()
    {
        if (empty($this->EOF) || 0 === $this->char) {
            return 1;
        }
        return substr_count($this->data, "\n", 0, min($this->char, $this->EOF)) + 1;
    }
    public function getCurrentLine()
    {
        return $this->currentLine();
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
    public function getColumnOffset()
    {
        return $this->columnOffset();
    }
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->data[$this->char];
    }
    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->char;
    }
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->char = 0;
    }
    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->char < $this->EOF;
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
    public function charsUntil($bytes, $max = null)
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
    public function charsWhile($bytes, $max = null)
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
    public function unconsume($howMany = 1)
    {
        if ($this->char - $howMany >= 0) {
            $this->char -= $howMany;
        }
    }
    public function peek()
    {
        if ($this->char + 1 <= $this->EOF) {
            return $this->data[$this->char + 1];
        }
        return \false;
    }
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->char;
    }
}
