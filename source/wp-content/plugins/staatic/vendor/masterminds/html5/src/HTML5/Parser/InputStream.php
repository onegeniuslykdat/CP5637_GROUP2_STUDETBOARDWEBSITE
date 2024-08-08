<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use Iterator;
interface InputStream extends Iterator
{
    public function currentLine();
    public function columnOffset();
    public function remainingChars();
    public function charsUntil($bytes, $max = null);
    public function charsWhile($bytes, $max = null);
    public function unconsume($howMany = 1);
    public function peek();
}
