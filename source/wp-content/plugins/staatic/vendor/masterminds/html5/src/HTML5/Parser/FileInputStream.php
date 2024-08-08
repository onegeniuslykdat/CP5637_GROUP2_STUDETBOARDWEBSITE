<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

class FileInputStream extends StringInputStream implements InputStream
{
    public function __construct($data, $encoding = 'UTF-8', $debug = '')
    {
        $content = file_get_contents($data);
        parent::__construct($content, $encoding, $debug);
    }
}
