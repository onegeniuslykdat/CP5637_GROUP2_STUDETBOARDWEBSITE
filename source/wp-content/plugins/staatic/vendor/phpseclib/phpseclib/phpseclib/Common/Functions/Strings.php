<?php

namespace Staatic\Vendor\phpseclib3\Common\Functions;

use LengthException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField\Integer;
use RuntimeException;
use Staatic\Vendor\ParagonIE\ConstantTime\Base64;
use Staatic\Vendor\ParagonIE\ConstantTime\Base64UrlSafe;
use Staatic\Vendor\ParagonIE\ConstantTime\Hex;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField;
abstract class Strings
{
    public static function shift(&$string, $index = 1)
    {
        $substr = substr($string, 0, $index);
        $string = substr($string, $index);
        return $substr;
    }
    public static function pop(&$string, $index = 1)
    {
        $substr = substr($string, -$index);
        $string = substr($string, 0, -$index);
        return $substr;
    }
    public static function unpackSSH2($format, &$data)
    {
        $format = self::formatPack($format);
        $result = [];
        for ($i = 0; $i < strlen($format); $i++) {
            switch ($format[$i]) {
                case 'C':
                case 'b':
                    if (!strlen($data)) {
                        throw new LengthException('At least one byte needs to be present for successful C / b decodes');
                    }
                    break;
                case 'N':
                case 'i':
                case 's':
                case 'L':
                    if (strlen($data) < 4) {
                        throw new LengthException('At least four byte needs to be present for successful N / i / s / L decodes');
                    }
                    break;
                case 'Q':
                    if (strlen($data) < 8) {
                        throw new LengthException('At least eight byte needs to be present for successful N / i / s / L decodes');
                    }
                    break;
                default:
                    throw new InvalidArgumentException('$format contains an invalid character');
            }
            switch ($format[$i]) {
                case 'C':
                    $result[] = ord(self::shift($data));
                    continue 2;
                case 'b':
                    $result[] = ord(self::shift($data)) != 0;
                    continue 2;
                case 'N':
                    list(, $temp) = unpack('N', self::shift($data, 4));
                    $result[] = $temp;
                    continue 2;
                case 'Q':
                    extract(unpack('Nupper/Nlower', self::shift($data, 8)));
                    $temp = $upper ? 4294967296 * $upper : 0;
                    $temp += ($lower < 0) ? ($lower & 0x7ffffffff) + 0x80000000 : $lower;
                    $result[] = $temp;
                    continue 2;
            }
            list(, $length) = unpack('N', self::shift($data, 4));
            if (strlen($data) < $length) {
                throw new LengthException("{$length} bytes needed; " . strlen($data) . ' bytes available');
            }
            $temp = self::shift($data, $length);
            switch ($format[$i]) {
                case 'i':
                    $result[] = new BigInteger($temp, -256);
                    break;
                case 's':
                    $result[] = $temp;
                    break;
                case 'L':
                    $result[] = explode(',', $temp);
            }
        }
        return $result;
    }
    public static function packSSH2($format, ...$elements)
    {
        $format = self::formatPack($format);
        if (strlen($format) != count($elements)) {
            throw new InvalidArgumentException('There must be as many arguments as there are characters in the $format string');
        }
        $result = '';
        for ($i = 0; $i < strlen($format); $i++) {
            $element = $elements[$i];
            switch ($format[$i]) {
                case 'C':
                    if (!is_int($element)) {
                        throw new InvalidArgumentException('Bytes must be represented as an integer between 0 and 255, inclusive.');
                    }
                    $result .= pack('C', $element);
                    break;
                case 'b':
                    if (!is_bool($element)) {
                        throw new InvalidArgumentException('A boolean parameter was expected.');
                    }
                    $result .= $element ? "\x01" : "\x00";
                    break;
                case 'Q':
                    if (!is_int($element) && !is_float($element)) {
                        throw new InvalidArgumentException('An integer was expected.');
                    }
                    $result .= pack('NN', $element / 4294967296, $element);
                    break;
                case 'N':
                    if (is_float($element)) {
                        $element = (int) $element;
                    }
                    if (!is_int($element)) {
                        throw new InvalidArgumentException('An integer was expected.');
                    }
                    $result .= pack('N', $element);
                    break;
                case 's':
                    if (!self::is_stringable($element)) {
                        throw new InvalidArgumentException('A string was expected.');
                    }
                    $result .= pack('Na*', strlen($element), $element);
                    break;
                case 'i':
                    if (!$element instanceof BigInteger && !$element instanceof Integer) {
                        throw new InvalidArgumentException('A phpseclib3\Math\BigInteger or phpseclib3\Math\Common\FiniteField\Integer object was expected.');
                    }
                    $element = $element->toBytes(\true);
                    $result .= pack('Na*', strlen($element), $element);
                    break;
                case 'L':
                    if (!is_array($element)) {
                        throw new InvalidArgumentException('An array was expected.');
                    }
                    $element = implode(',', $element);
                    $result .= pack('Na*', strlen($element), $element);
                    break;
                default:
                    throw new InvalidArgumentException('$format contains an invalid character');
            }
        }
        return $result;
    }
    private static function formatPack($format)
    {
        $parts = preg_split('#(\d+)#', $format, -1, \PREG_SPLIT_DELIM_CAPTURE);
        $format = '';
        for ($i = 1; $i < count($parts); $i += 2) {
            $format .= substr($parts[$i - 1], 0, -1) . str_repeat(substr($parts[$i - 1], -1), $parts[$i]);
        }
        $format .= $parts[$i - 1];
        return $format;
    }
    public static function bits2bin($x)
    {
        if (preg_match('#[^01]#', $x)) {
            throw new RuntimeException('The only valid characters are 0 and 1');
        }
        if (!defined('PHP_INT_MIN')) {
            define('PHP_INT_MIN', ~\PHP_INT_MAX);
        }
        $length = strlen($x);
        if (!$length) {
            return '';
        }
        $block_size = \PHP_INT_SIZE << 3;
        $pad = $block_size - $length % $block_size;
        if ($pad != $block_size) {
            $x = str_repeat('0', $pad) . $x;
        }
        $parts = str_split($x, $block_size);
        $str = '';
        foreach ($parts as $part) {
            $xor = ($part[0] == '1') ? \PHP_INT_MIN : 0;
            $part[0] = '0';
            $str .= pack((\PHP_INT_SIZE == 4) ? 'N' : 'J', $xor ^ eval('return 0b' . $part . ';'));
        }
        return ltrim($str, "\x00");
    }
    public static function bin2bits($x, $trim = \true)
    {
        $len = strlen($x);
        $mod = $len % \PHP_INT_SIZE;
        if ($mod) {
            $x = str_pad($x, $len + \PHP_INT_SIZE - $mod, "\x00", \STR_PAD_LEFT);
        }
        $bits = '';
        if (\PHP_INT_SIZE == 4) {
            $digits = unpack('N*', $x);
            foreach ($digits as $digit) {
                $bits .= sprintf('%032b', $digit);
            }
        } else {
            $digits = unpack('J*', $x);
            foreach ($digits as $digit) {
                $bits .= sprintf('%064b', $digit);
            }
        }
        return $trim ? ltrim($bits, '0') : $bits;
    }
    public static function switchEndianness($x)
    {
        $r = '';
        for ($i = strlen($x) - 1; $i >= 0; $i--) {
            $b = ord($x[$i]);
            if (\PHP_INT_SIZE === 8) {
                $r .= chr(($b * 0x202020202 & 0x10884422010) % 1023);
            } else {
                $p1 = $b * 0x802 & 0x22110;
                $p2 = $b * 0x8020 & 0x88440;
                $r .= chr(($p1 | $p2) * 0x10101 >> 16);
            }
        }
        return $r;
    }
    public static function increment_str(&$var)
    {
        if (function_exists('sodium_increment')) {
            $var = strrev($var);
            sodium_increment($var);
            $var = strrev($var);
            return $var;
        }
        for ($i = 4; $i <= strlen($var); $i += 4) {
            $temp = substr($var, -$i, 4);
            switch ($temp) {
                case "\xff\xff\xff\xff":
                    $var = substr_replace($var, "\x00\x00\x00\x00", -$i, 4);
                    break;
                case "\xff\xff\xff":
                    $var = substr_replace($var, "\x80\x00\x00\x00", -$i, 4);
                    return $var;
                default:
                    $temp = unpack('Nnum', $temp);
                    $var = substr_replace($var, pack('N', $temp['num'] + 1), -$i, 4);
                    return $var;
            }
        }
        $remainder = strlen($var) % 4;
        if ($remainder == 0) {
            return $var;
        }
        $temp = unpack('Nnum', str_pad(substr($var, 0, $remainder), 4, "\x00", \STR_PAD_LEFT));
        $temp = substr(pack('N', $temp['num'] + 1), -$remainder);
        $var = substr_replace($var, $temp, 0, $remainder);
        return $var;
    }
    public static function is_stringable($var)
    {
        return is_string($var) || is_object($var) && method_exists($var, '__toString');
    }
    public static function base64_decode($data)
    {
        return function_exists('sodium_base642bin') ? sodium_base642bin($data, \SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING, '=') : Base64::decode($data);
    }
    public static function base64url_decode($data)
    {
        return function_exists('sodium_base642bin') ? sodium_base642bin($data, \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING, '=') : Base64UrlSafe::decode($data);
    }
    public static function base64_encode($data)
    {
        return function_exists('sodium_bin2base64') ? sodium_bin2base64($data, \SODIUM_BASE64_VARIANT_ORIGINAL) : Base64::encode($data);
    }
    public static function base64url_encode($data)
    {
        return function_exists('sodium_bin2base64') ? sodium_bin2base64($data, \SODIUM_BASE64_VARIANT_URLSAFE) : Base64UrlSafe::encode($data);
    }
    public static function hex2bin($data)
    {
        return function_exists('sodium_hex2bin') ? sodium_hex2bin($data) : Hex::decode($data);
    }
    public static function bin2hex($data)
    {
        return function_exists('sodium_bin2hex') ? sodium_bin2hex($data) : Hex::encode($data);
    }
}
