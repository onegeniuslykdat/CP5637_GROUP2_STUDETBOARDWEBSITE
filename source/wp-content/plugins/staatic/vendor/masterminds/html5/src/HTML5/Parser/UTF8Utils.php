<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

use Staatic\Vendor\Masterminds\HTML5\Exception;
class UTF8Utils
{
    const FFFD = "�";
    public static function countChars($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, 'utf-8');
        }
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($string, 'utf-8');
        }
        $count = count_chars($string);
        return array_sum(array_slice($count, 0, 0x80)) + array_sum(array_slice($count, 0xc2, 0x33));
    }
    public static function convertToUTF8($data, $encoding = 'UTF-8')
    {
        if (function_exists('mb_convert_encoding')) {
            $save = mb_substitute_character();
            mb_substitute_character('none');
            $data = mb_convert_encoding($data, 'UTF-8', $encoding);
            mb_substitute_character($save);
        } elseif (function_exists('iconv') && 'auto' !== $encoding) {
            $data = @iconv($encoding, 'UTF-8//IGNORE', $data);
        } else {
            throw new Exception('Not implemented, please install mbstring or iconv');
        }
        if ("﻿" === substr($data, 0, 3)) {
            $data = substr($data, 3);
        }
        return $data;
    }
    public static function checkForIllegalCodepoints($data)
    {
        $errors = array();
        for ($i = 0, $count = substr_count($data, "\x00"); $i < $count; ++$i) {
            $errors[] = 'null-character';
        }
        $count = preg_match_all('/(?:
        [\x01-\x08\x0B\x0E-\x1F\x7F] # U+0001 to U+0008, U+000B,  U+000E to U+001F and U+007F
      |
        \xC2[\x80-\x9F] # U+0080 to U+009F
      |
        \xED(?:\xA0[\x80-\xFF]|[\xA1-\xBE][\x00-\xFF]|\xBF[\x00-\xBF]) # U+D800 to U+DFFFF
      |
        \xEF\xB7[\x90-\xAF] # U+FDD0 to U+FDEF
      |
        \xEF\xBF[\xBE\xBF] # U+FFFE and U+FFFF
      |
        [\xF0-\xF4][\x8F-\xBF]\xBF[\xBE\xBF] # U+nFFFE and U+nFFFF (1 <= n <= 10_{16})
      )/x', $data, $matches);
        for ($i = 0; $i < $count; ++$i) {
            $errors[] = 'invalid-codepoint';
        }
        return $errors;
    }
}
