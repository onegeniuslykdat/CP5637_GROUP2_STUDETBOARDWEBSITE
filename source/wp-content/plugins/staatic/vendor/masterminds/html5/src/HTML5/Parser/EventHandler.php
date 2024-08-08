<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

interface EventHandler
{
    const DOCTYPE_NONE = 0;
    const DOCTYPE_PUBLIC = 1;
    const DOCTYPE_SYSTEM = 2;
    public function doctype($name, $idType = 0, $id = null, $quirks = \false);
    public function startTag($name, $attributes = array(), $selfClosing = \false);
    public function endTag($name);
    public function comment($cdata);
    public function text($cdata);
    public function eof();
    public function parseError($msg, $line, $col);
    public function cdata($data);
    public function processingInstruction($name, $data = null);
}
