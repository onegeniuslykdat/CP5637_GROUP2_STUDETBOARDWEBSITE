<?php

namespace Staatic\Framework\Util;

final class PathEncoder
{
    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    public static function encode(string $path): string
    {
        return preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function ($match) {
            return rawurlencode($match[0]);
        }, $path);
    }
}
