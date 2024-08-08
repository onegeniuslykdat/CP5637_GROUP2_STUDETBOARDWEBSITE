<?php

namespace Staatic\Vendor\phpseclib3\File;

use DateTime;
use DateTimeZone;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class ASN1
{
    const CLASS_UNIVERSAL = 0;
    const CLASS_APPLICATION = 1;
    const CLASS_CONTEXT_SPECIFIC = 2;
    const CLASS_PRIVATE = 3;
    const TYPE_BOOLEAN = 1;
    const TYPE_INTEGER = 2;
    const TYPE_BIT_STRING = 3;
    const TYPE_OCTET_STRING = 4;
    const TYPE_NULL = 5;
    const TYPE_OBJECT_IDENTIFIER = 6;
    const TYPE_REAL = 9;
    const TYPE_ENUMERATED = 10;
    const TYPE_UTF8_STRING = 12;
    const TYPE_SEQUENCE = 16;
    const TYPE_SET = 17;
    const TYPE_NUMERIC_STRING = 18;
    const TYPE_PRINTABLE_STRING = 19;
    const TYPE_TELETEX_STRING = 20;
    const TYPE_VIDEOTEX_STRING = 21;
    const TYPE_IA5_STRING = 22;
    const TYPE_UTC_TIME = 23;
    const TYPE_GENERALIZED_TIME = 24;
    const TYPE_GRAPHIC_STRING = 25;
    const TYPE_VISIBLE_STRING = 26;
    const TYPE_GENERAL_STRING = 27;
    const TYPE_UNIVERSAL_STRING = 28;
    const TYPE_BMP_STRING = 30;
    const TYPE_CHOICE = -1;
    const TYPE_ANY = -2;
    private static $oids = [];
    private static $reverseOIDs = [];
    private static $format = 'D, d M Y H:i:s O';
    private static $filters;
    private static $location;
    private static $encoded;
    const ANY_MAP = [self::TYPE_BOOLEAN => \true, self::TYPE_INTEGER => \true, self::TYPE_BIT_STRING => 'bitString', self::TYPE_OCTET_STRING => 'octetString', self::TYPE_NULL => 'null', self::TYPE_OBJECT_IDENTIFIER => 'objectIdentifier', self::TYPE_REAL => \true, self::TYPE_ENUMERATED => 'enumerated', self::TYPE_UTF8_STRING => 'utf8String', self::TYPE_NUMERIC_STRING => 'numericString', self::TYPE_PRINTABLE_STRING => 'printableString', self::TYPE_TELETEX_STRING => 'teletexString', self::TYPE_VIDEOTEX_STRING => 'videotexString', self::TYPE_IA5_STRING => 'ia5String', self::TYPE_UTC_TIME => 'utcTime', self::TYPE_GENERALIZED_TIME => 'generalTime', self::TYPE_GRAPHIC_STRING => 'graphicString', self::TYPE_VISIBLE_STRING => 'visibleString', self::TYPE_GENERAL_STRING => 'generalString', self::TYPE_UNIVERSAL_STRING => 'universalString', self::TYPE_BMP_STRING => 'bmpString'];
    const STRING_TYPE_SIZE = [self::TYPE_UTF8_STRING => 0, self::TYPE_BMP_STRING => 2, self::TYPE_UNIVERSAL_STRING => 4, self::TYPE_PRINTABLE_STRING => 1, self::TYPE_TELETEX_STRING => 1, self::TYPE_IA5_STRING => 1, self::TYPE_VISIBLE_STRING => 1];
    public static function decodeBER($encoded)
    {
        if ($encoded instanceof Element) {
            $encoded = $encoded->element;
        }
        self::$encoded = $encoded;
        $decoded = self::decode_ber($encoded);
        if ($decoded === \false) {
            return null;
        }
        return [$decoded];
    }
    private static function decode_ber($encoded, $start = 0, $encoded_pos = 0)
    {
        $current = ['start' => $start];
        if (!isset($encoded[$encoded_pos])) {
            return \false;
        }
        $type = ord($encoded[$encoded_pos++]);
        $startOffset = 1;
        $constructed = $type >> 5 & 1;
        $tag = $type & 0x1f;
        if ($tag == 0x1f) {
            $tag = 0;
            do {
                if (!isset($encoded[$encoded_pos])) {
                    return \false;
                }
                $temp = ord($encoded[$encoded_pos++]);
                $startOffset++;
                $loop = $temp >> 7;
                $tag <<= 7;
                $temp &= 0x7f;
                if ($startOffset == 2 && $temp == 0) {
                    return \false;
                }
                $tag |= $temp;
            } while ($loop);
        }
        $start += $startOffset;
        if (!isset($encoded[$encoded_pos])) {
            return \false;
        }
        $length = ord($encoded[$encoded_pos++]);
        $start++;
        if ($length == 0x80) {
            $length = strlen($encoded) - $encoded_pos;
        } elseif ($length & 0x80) {
            $length &= 0x7f;
            $temp = substr($encoded, $encoded_pos, $length);
            $encoded_pos += $length;
            $current += ['headerlength' => $length + 2];
            $start += $length;
            extract(unpack('Nlength', substr(str_pad($temp, 4, chr(0), \STR_PAD_LEFT), -4)));
        } else {
            $current += ['headerlength' => 2];
        }
        if ($length > strlen($encoded) - $encoded_pos) {
            return \false;
        }
        $content = substr($encoded, $encoded_pos, $length);
        $content_pos = 0;
        $class = $type >> 6 & 3;
        switch ($class) {
            case self::CLASS_APPLICATION:
            case self::CLASS_PRIVATE:
            case self::CLASS_CONTEXT_SPECIFIC:
                if (!$constructed) {
                    return ['type' => $class, 'constant' => $tag, 'content' => $content, 'length' => $length + $start - $current['start']] + $current;
                }
                $newcontent = [];
                $remainingLength = $length;
                while ($remainingLength > 0) {
                    $temp = self::decode_ber($content, $start, $content_pos);
                    if ($temp === \false) {
                        break;
                    }
                    $length = $temp['length'];
                    if (substr($content, $content_pos + $length, 2) == "\x00\x00") {
                        $length += 2;
                        $start += $length;
                        $newcontent[] = $temp;
                        break;
                    }
                    $start += $length;
                    $remainingLength -= $length;
                    $newcontent[] = $temp;
                    $content_pos += $length;
                }
                return ['type' => $class, 'constant' => $tag, 'content' => $newcontent, 'length' => $start - $current['start']] + $current;
        }
        $current += ['type' => $tag];
        switch ($tag) {
            case self::TYPE_BOOLEAN:
                if ($constructed || strlen($content) != 1) {
                    return \false;
                }
                $current['content'] = (bool) ord($content[$content_pos]);
                break;
            case self::TYPE_INTEGER:
            case self::TYPE_ENUMERATED:
                if ($constructed) {
                    return \false;
                }
                $current['content'] = new BigInteger(substr($content, $content_pos), -256);
                break;
            case self::TYPE_REAL:
                return \false;
            case self::TYPE_BIT_STRING:
                if (!$constructed) {
                    $current['content'] = substr($content, $content_pos);
                } else {
                    $temp = self::decode_ber($content, $start, $content_pos);
                    if ($temp === \false) {
                        return \false;
                    }
                    $length -= strlen($content) - $content_pos;
                    $last = count($temp) - 1;
                    for ($i = 0; $i < $last; $i++) {
                        if ($temp[$i]['type'] != self::TYPE_BIT_STRING) {
                            return \false;
                        }
                        $current['content'] .= substr($temp[$i]['content'], 1);
                    }
                    if ($temp[$last]['type'] != self::TYPE_BIT_STRING) {
                        return \false;
                    }
                    $current['content'] = $temp[$last]['content'][0] . $current['content'] . substr($temp[$i]['content'], 1);
                }
                break;
            case self::TYPE_OCTET_STRING:
                if (!$constructed) {
                    $current['content'] = substr($content, $content_pos);
                } else {
                    $current['content'] = '';
                    $length = 0;
                    while (substr($content, $content_pos, 2) != "\x00\x00") {
                        $temp = self::decode_ber($content, $length + $start, $content_pos);
                        if ($temp === \false) {
                            return \false;
                        }
                        $content_pos += $temp['length'];
                        if ($temp['type'] != self::TYPE_OCTET_STRING) {
                            return \false;
                        }
                        $current['content'] .= $temp['content'];
                        $length += $temp['length'];
                    }
                    if (substr($content, $content_pos, 2) == "\x00\x00") {
                        $length += 2;
                    }
                }
                break;
            case self::TYPE_NULL:
                if ($constructed || strlen($content)) {
                    return \false;
                }
                break;
            case self::TYPE_SEQUENCE:
            case self::TYPE_SET:
                if (!$constructed) {
                    return \false;
                }
                $offset = 0;
                $current['content'] = [];
                $content_len = strlen($content);
                while ($content_pos < $content_len) {
                    if (!isset($current['headerlength']) && substr($content, $content_pos, 2) == "\x00\x00") {
                        $length = $offset + 2;
                        break 2;
                    }
                    $temp = self::decode_ber($content, $start + $offset, $content_pos);
                    if ($temp === \false) {
                        return \false;
                    }
                    $content_pos += $temp['length'];
                    $current['content'][] = $temp;
                    $offset += $temp['length'];
                }
                break;
            case self::TYPE_OBJECT_IDENTIFIER:
                if ($constructed) {
                    return \false;
                }
                $current['content'] = self::decodeOID(substr($content, $content_pos));
                if ($current['content'] === \false) {
                    return \false;
                }
                break;
            case self::TYPE_NUMERIC_STRING:
            case self::TYPE_PRINTABLE_STRING:
            case self::TYPE_TELETEX_STRING:
            case self::TYPE_VIDEOTEX_STRING:
            case self::TYPE_VISIBLE_STRING:
            case self::TYPE_IA5_STRING:
            case self::TYPE_GRAPHIC_STRING:
            case self::TYPE_GENERAL_STRING:
            case self::TYPE_UTF8_STRING:
            case self::TYPE_BMP_STRING:
                if ($constructed) {
                    return \false;
                }
                $current['content'] = substr($content, $content_pos);
                break;
            case self::TYPE_UTC_TIME:
            case self::TYPE_GENERALIZED_TIME:
                if ($constructed) {
                    return \false;
                }
                $current['content'] = self::decodeTime(substr($content, $content_pos), $tag);
                break;
            default:
                return \false;
        }
        $start += $length;
        return $current + ['length' => $start - $current['start']];
    }
    /**
     * @param mixed[] $decoded
     */
    public static function asn1map($decoded, $mapping, $special = [])
    {
        if (isset($mapping['explicit']) && is_array($decoded['content'])) {
            $decoded = $decoded['content'][0];
        }
        switch (\true) {
            case $mapping['type'] == self::TYPE_ANY:
                $intype = $decoded['type'];
                if (isset($decoded['constant']) || !array_key_exists($intype, self::ANY_MAP) || ord(self::$encoded[$decoded['start']]) & 0x20) {
                    return new Element(substr(self::$encoded, $decoded['start'], $decoded['length']));
                }
                $inmap = self::ANY_MAP[$intype];
                if (is_string($inmap)) {
                    return [$inmap => self::asn1map($decoded, ['type' => $intype] + $mapping, $special)];
                }
                break;
            case $mapping['type'] == self::TYPE_CHOICE:
                foreach ($mapping['children'] as $key => $option) {
                    switch (\true) {
                        case isset($option['constant']) && $option['constant'] == $decoded['constant']:
                        case !isset($option['constant']) && $option['type'] == $decoded['type']:
                            $value = self::asn1map($decoded, $option, $special);
                            break;
                        case !isset($option['constant']) && $option['type'] == self::TYPE_CHOICE:
                            $v = self::asn1map($decoded, $option, $special);
                            if (isset($v)) {
                                $value = $v;
                            }
                    }
                    if (isset($value)) {
                        if (isset($special[$key])) {
                            $value = $special[$key]($value);
                        }
                        return [$key => $value];
                    }
                }
                return null;
            case isset($mapping['implicit']):
            case isset($mapping['explicit']):
            case $decoded['type'] == $mapping['type']:
                break;
            default:
                switch (\true) {
                    case $decoded['type'] < 18:
                    case $decoded['type'] > 30:
                    case $mapping['type'] < 18:
                    case $mapping['type'] > 30:
                        return null;
                }
        }
        if (isset($mapping['implicit'])) {
            $decoded['type'] = $mapping['type'];
        }
        switch ($decoded['type']) {
            case self::TYPE_SEQUENCE:
                $map = [];
                if (isset($mapping['min']) && isset($mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($decoded['content'] as $content) {
                        if (($map[] = self::asn1map($content, $child, $special)) === null) {
                            return null;
                        }
                    }
                    return $map;
                }
                $n = count($decoded['content']);
                $i = 0;
                foreach ($mapping['children'] as $key => $child) {
                    $maymatch = $i < $n;
                    if ($maymatch) {
                        $temp = $decoded['content'][$i];
                        if ($child['type'] != self::TYPE_CHOICE) {
                            $childClass = $tempClass = self::CLASS_UNIVERSAL;
                            $constant = null;
                            if (isset($temp['constant'])) {
                                $tempClass = $temp['type'];
                            }
                            if (isset($child['class'])) {
                                $childClass = $child['class'];
                                $constant = $child['cast'];
                            } elseif (isset($child['constant'])) {
                                $childClass = self::CLASS_CONTEXT_SPECIFIC;
                                $constant = $child['constant'];
                            }
                            if (isset($constant) && isset($temp['constant'])) {
                                $maymatch = $constant == $temp['constant'] && $childClass == $tempClass;
                            } else {
                                $maymatch = !isset($child['constant']) && array_search($child['type'], [$temp['type'], self::TYPE_ANY, self::TYPE_CHOICE]) !== \false;
                            }
                        }
                    }
                    if ($maymatch) {
                        $candidate = self::asn1map($temp, $child, $special);
                        $maymatch = $candidate !== null;
                    }
                    if ($maymatch) {
                        if (isset($special[$key])) {
                            $candidate = $special[$key]($candidate);
                        }
                        $map[$key] = $candidate;
                        $i++;
                    } elseif (isset($child['default'])) {
                        $map[$key] = $child['default'];
                    } elseif (!isset($child['optional'])) {
                        return null;
                    }
                }
                return ($i < $n) ? null : $map;
            case self::TYPE_SET:
                $map = [];
                if (isset($mapping['min']) && isset($mapping['max'])) {
                    $child = $mapping['children'];
                    foreach ($decoded['content'] as $content) {
                        if (($map[] = self::asn1map($content, $child, $special)) === null) {
                            return null;
                        }
                    }
                    return $map;
                }
                for ($i = 0; $i < count($decoded['content']); $i++) {
                    $temp = $decoded['content'][$i];
                    $tempClass = self::CLASS_UNIVERSAL;
                    if (isset($temp['constant'])) {
                        $tempClass = $temp['type'];
                    }
                    foreach ($mapping['children'] as $key => $child) {
                        if (isset($map[$key])) {
                            continue;
                        }
                        $maymatch = \true;
                        if ($child['type'] != self::TYPE_CHOICE) {
                            $childClass = self::CLASS_UNIVERSAL;
                            $constant = null;
                            if (isset($child['class'])) {
                                $childClass = $child['class'];
                                $constant = $child['cast'];
                            } elseif (isset($child['constant'])) {
                                $childClass = self::CLASS_CONTEXT_SPECIFIC;
                                $constant = $child['constant'];
                            }
                            if (isset($constant) && isset($temp['constant'])) {
                                $maymatch = $constant == $temp['constant'] && $childClass == $tempClass;
                            } else {
                                $maymatch = !isset($child['constant']) && array_search($child['type'], [$temp['type'], self::TYPE_ANY, self::TYPE_CHOICE]) !== \false;
                            }
                        }
                        if ($maymatch) {
                            $candidate = self::asn1map($temp, $child, $special);
                            $maymatch = $candidate !== null;
                        }
                        if (!$maymatch) {
                            break;
                        }
                        if (isset($special[$key])) {
                            $candidate = $special[$key]($candidate);
                        }
                        $map[$key] = $candidate;
                        break;
                    }
                }
                foreach ($mapping['children'] as $key => $child) {
                    if (!isset($map[$key])) {
                        if (isset($child['default'])) {
                            $map[$key] = $child['default'];
                        } elseif (!isset($child['optional'])) {
                            return null;
                        }
                    }
                }
                return $map;
            case self::TYPE_OBJECT_IDENTIFIER:
                return isset(self::$oids[$decoded['content']]) ? self::$oids[$decoded['content']] : $decoded['content'];
            case self::TYPE_UTC_TIME:
            case self::TYPE_GENERALIZED_TIME:
                if (is_array($decoded['content'])) {
                    $decoded['content'] = $decoded['content'][0]['content'];
                }
                if (!is_object($decoded['content'])) {
                    $decoded['content'] = self::decodeTime($decoded['content'], $decoded['type']);
                }
                return $decoded['content'] ? $decoded['content']->format(self::$format) : \false;
            case self::TYPE_BIT_STRING:
                if (isset($mapping['mapping'])) {
                    $offset = ord($decoded['content'][0]);
                    $size = (strlen($decoded['content']) - 1) * 8 - $offset;
                    $bits = (count($mapping['mapping']) == $size) ? [] : array_fill(0, count($mapping['mapping']) - $size, \false);
                    for ($i = strlen($decoded['content']) - 1; $i > 0; $i--) {
                        $current = ord($decoded['content'][$i]);
                        for ($j = $offset; $j < 8; $j++) {
                            $bits[] = (bool) ($current & 1 << $j);
                        }
                        $offset = 0;
                    }
                    $values = [];
                    $map = array_reverse($mapping['mapping']);
                    foreach ($map as $i => $value) {
                        if ($bits[$i]) {
                            $values[] = $value;
                        }
                    }
                    return $values;
                }
            case self::TYPE_OCTET_STRING:
                return $decoded['content'];
            case self::TYPE_NULL:
                return '';
            case self::TYPE_BOOLEAN:
            case self::TYPE_NUMERIC_STRING:
            case self::TYPE_PRINTABLE_STRING:
            case self::TYPE_TELETEX_STRING:
            case self::TYPE_VIDEOTEX_STRING:
            case self::TYPE_IA5_STRING:
            case self::TYPE_GRAPHIC_STRING:
            case self::TYPE_VISIBLE_STRING:
            case self::TYPE_GENERAL_STRING:
            case self::TYPE_UNIVERSAL_STRING:
            case self::TYPE_UTF8_STRING:
            case self::TYPE_BMP_STRING:
                return $decoded['content'];
            case self::TYPE_INTEGER:
            case self::TYPE_ENUMERATED:
                $temp = $decoded['content'];
                if (isset($mapping['implicit'])) {
                    $temp = new BigInteger($decoded['content'], -256);
                }
                if (isset($mapping['mapping'])) {
                    $temp = (int) $temp->toString();
                    return isset($mapping['mapping'][$temp]) ? $mapping['mapping'][$temp] : \false;
                }
                return $temp;
        }
    }
    public static function decodeLength(&$string)
    {
        $length = ord(Strings::shift($string));
        if ($length & 0x80) {
            $length &= 0x7f;
            $temp = Strings::shift($string, $length);
            list(, $length) = unpack('N', substr(str_pad($temp, 4, chr(0), \STR_PAD_LEFT), -4));
        }
        return $length;
    }
    public static function encodeDER($source, $mapping, $special = [])
    {
        self::$location = [];
        return self::encode_der($source, $mapping, null, $special);
    }
    private static function encode_der($source, array $mapping, $idx = null, array $special = [])
    {
        if ($source instanceof Element) {
            return $source->element;
        }
        if (isset($mapping['default']) && $source === $mapping['default']) {
            return '';
        }
        if (isset($idx)) {
            if (isset($special[$idx])) {
                $source = $special[$idx]($source);
            }
            self::$location[] = $idx;
        }
        $tag = $mapping['type'];
        switch ($tag) {
            case self::TYPE_SET:
            case self::TYPE_SEQUENCE:
                $tag |= 0x20;
                if (isset($mapping['min']) && isset($mapping['max'])) {
                    $value = [];
                    $child = $mapping['children'];
                    foreach ($source as $content) {
                        $temp = self::encode_der($content, $child, null, $special);
                        if ($temp === \false) {
                            return \false;
                        }
                        $value[] = $temp;
                    }
                    if ($mapping['type'] == self::TYPE_SET) {
                        sort($value);
                    }
                    $value = implode('', $value);
                    break;
                }
                $value = '';
                foreach ($mapping['children'] as $key => $child) {
                    if (!array_key_exists($key, $source)) {
                        if (!isset($child['optional'])) {
                            return \false;
                        }
                        continue;
                    }
                    $temp = self::encode_der($source[$key], $child, $key, $special);
                    if ($temp === \false) {
                        return \false;
                    }
                    if ($temp === '') {
                        continue;
                    }
                    if (isset($child['constant'])) {
                        if (isset($child['explicit']) || $child['type'] == self::TYPE_CHOICE) {
                            $subtag = chr(self::CLASS_CONTEXT_SPECIFIC << 6 | 0x20 | $child['constant']);
                            $temp = $subtag . self::encodeLength(strlen($temp)) . $temp;
                        } else {
                            $subtag = chr(self::CLASS_CONTEXT_SPECIFIC << 6 | ord($temp[0]) & 0x20 | $child['constant']);
                            $temp = $subtag . substr($temp, 1);
                        }
                    }
                    $value .= $temp;
                }
                break;
            case self::TYPE_CHOICE:
                $temp = \false;
                foreach ($mapping['children'] as $key => $child) {
                    if (!isset($source[$key])) {
                        continue;
                    }
                    $temp = self::encode_der($source[$key], $child, $key, $special);
                    if ($temp === \false) {
                        return \false;
                    }
                    if ($temp === '') {
                        continue;
                    }
                    $tag = ord($temp[0]);
                    if (isset($child['constant'])) {
                        if (isset($child['explicit']) || $child['type'] == self::TYPE_CHOICE) {
                            $subtag = chr(self::CLASS_CONTEXT_SPECIFIC << 6 | 0x20 | $child['constant']);
                            $temp = $subtag . self::encodeLength(strlen($temp)) . $temp;
                        } else {
                            $subtag = chr(self::CLASS_CONTEXT_SPECIFIC << 6 | ord($temp[0]) & 0x20 | $child['constant']);
                            $temp = $subtag . substr($temp, 1);
                        }
                    }
                }
                if (isset($idx)) {
                    array_pop(self::$location);
                }
                if ($temp && isset($mapping['cast'])) {
                    $temp[0] = chr($mapping['class'] << 6 | $tag & 0x20 | $mapping['cast']);
                }
                return $temp;
            case self::TYPE_INTEGER:
            case self::TYPE_ENUMERATED:
                if (!isset($mapping['mapping'])) {
                    if (is_numeric($source)) {
                        $source = new BigInteger($source);
                    }
                    $value = $source->toBytes(\true);
                } else {
                    $value = array_search($source, $mapping['mapping']);
                    if ($value === \false) {
                        return \false;
                    }
                    $value = new BigInteger($value);
                    $value = $value->toBytes(\true);
                }
                if (!strlen($value)) {
                    $value = chr(0);
                }
                break;
            case self::TYPE_UTC_TIME:
            case self::TYPE_GENERALIZED_TIME:
                $format = ($mapping['type'] == self::TYPE_UTC_TIME) ? 'y' : 'Y';
                $format .= 'mdHis';
                $date = new DateTime($source, new DateTimeZone('GMT'));
                $date->setTimezone(new DateTimeZone('GMT'));
                $value = $date->format($format) . 'Z';
                break;
            case self::TYPE_BIT_STRING:
                if (isset($mapping['mapping'])) {
                    $bits = array_fill(0, count($mapping['mapping']), 0);
                    $size = 0;
                    for ($i = 0; $i < count($mapping['mapping']); $i++) {
                        if (in_array($mapping['mapping'][$i], $source)) {
                            $bits[$i] = 1;
                            $size = $i;
                        }
                    }
                    if (isset($mapping['min']) && $mapping['min'] >= 1 && $size < $mapping['min']) {
                        $size = $mapping['min'] - 1;
                    }
                    $offset = 8 - ($size + 1 & 7);
                    $offset = ($offset !== 8) ? $offset : 0;
                    $value = chr($offset);
                    for ($i = $size + 1; $i < count($mapping['mapping']); $i++) {
                        unset($bits[$i]);
                    }
                    $bits = implode('', array_pad($bits, $size + $offset + 1, 0));
                    $bytes = explode(' ', rtrim(chunk_split($bits, 8, ' ')));
                    foreach ($bytes as $byte) {
                        $value .= chr(bindec($byte));
                    }
                    break;
                }
            case self::TYPE_OCTET_STRING:
                $value = $source;
                break;
            case self::TYPE_OBJECT_IDENTIFIER:
                $value = self::encodeOID($source);
                break;
            case self::TYPE_ANY:
                $loc = self::$location;
                if (isset($idx)) {
                    array_pop(self::$location);
                }
                switch (\true) {
                    case !isset($source):
                        return self::encode_der(null, ['type' => self::TYPE_NULL] + $mapping, null, $special);
                    case is_int($source):
                    case $source instanceof BigInteger:
                        return self::encode_der($source, ['type' => self::TYPE_INTEGER] + $mapping, null, $special);
                    case is_float($source):
                        return self::encode_der($source, ['type' => self::TYPE_REAL] + $mapping, null, $special);
                    case is_bool($source):
                        return self::encode_der($source, ['type' => self::TYPE_BOOLEAN] + $mapping, null, $special);
                    case is_array($source) && count($source) == 1:
                        $typename = implode('', array_keys($source));
                        $outtype = array_search($typename, self::ANY_MAP, \true);
                        if ($outtype !== \false) {
                            return self::encode_der($source[$typename], ['type' => $outtype] + $mapping, null, $special);
                        }
                }
                $filters = self::$filters;
                foreach ($loc as $part) {
                    if (!isset($filters[$part])) {
                        $filters = \false;
                        break;
                    }
                    $filters = $filters[$part];
                }
                if ($filters === \false) {
                    throw new RuntimeException('No filters defined for ' . implode('/', $loc));
                }
                return self::encode_der($source, $filters + $mapping, null, $special);
            case self::TYPE_NULL:
                $value = '';
                break;
            case self::TYPE_NUMERIC_STRING:
            case self::TYPE_TELETEX_STRING:
            case self::TYPE_PRINTABLE_STRING:
            case self::TYPE_UNIVERSAL_STRING:
            case self::TYPE_UTF8_STRING:
            case self::TYPE_BMP_STRING:
            case self::TYPE_IA5_STRING:
            case self::TYPE_VISIBLE_STRING:
            case self::TYPE_VIDEOTEX_STRING:
            case self::TYPE_GRAPHIC_STRING:
            case self::TYPE_GENERAL_STRING:
                $value = $source;
                break;
            case self::TYPE_BOOLEAN:
                $value = $source ? "\xff" : "\x00";
                break;
            default:
                throw new RuntimeException('Mapping provides no type definition for ' . implode('/', self::$location));
        }
        if (isset($idx)) {
            array_pop(self::$location);
        }
        if (isset($mapping['cast'])) {
            if (isset($mapping['explicit']) || $mapping['type'] == self::TYPE_CHOICE) {
                $value = chr($tag) . self::encodeLength(strlen($value)) . $value;
                $tag = $mapping['class'] << 6 | 0x20 | $mapping['cast'];
            } else {
                $tag = $mapping['class'] << 6 | ord($temp[0]) & 0x20 | $mapping['cast'];
            }
        }
        return chr($tag) . self::encodeLength(strlen($value)) . $value;
    }
    public static function decodeOID($content)
    {
        static $eighty;
        if (!$eighty) {
            $eighty = new BigInteger(80);
        }
        $oid = [];
        $pos = 0;
        $len = strlen($content);
        if ($len > 4096) {
            return \false;
        }
        if (ord($content[$len - 1]) & 0x80) {
            return \false;
        }
        $n = new BigInteger();
        while ($pos < $len) {
            $temp = ord($content[$pos++]);
            $n = $n->bitwise_leftShift(7);
            $n = $n->bitwise_or(new BigInteger($temp & 0x7f));
            if (~$temp & 0x80) {
                $oid[] = $n;
                $n = new BigInteger();
            }
        }
        $part1 = array_shift($oid);
        $first = floor(ord($content[0]) / 40);
        if ($first <= 2) {
            array_unshift($oid, ord($content[0]) % 40);
            array_unshift($oid, $first);
        } else {
            array_unshift($oid, $part1->subtract($eighty));
            array_unshift($oid, 2);
        }
        return implode('.', $oid);
    }
    public static function encodeOID($source)
    {
        static $mask, $zero, $forty;
        if (!$mask) {
            $mask = new BigInteger(0x7f);
            $zero = new BigInteger();
            $forty = new BigInteger(40);
        }
        if (!preg_match('#(?:\d+\.)+#', $source)) {
            $oid = isset(self::$reverseOIDs[$source]) ? self::$reverseOIDs[$source] : \false;
        } else {
            $oid = $source;
        }
        if ($oid === \false) {
            throw new RuntimeException('Invalid OID');
        }
        $parts = explode('.', $oid);
        $part1 = array_shift($parts);
        $part2 = array_shift($parts);
        $first = new BigInteger($part1);
        $first = $first->multiply($forty);
        $first = $first->add(new BigInteger($part2));
        array_unshift($parts, $first->toString());
        $value = '';
        foreach ($parts as $part) {
            if (!$part) {
                $temp = "\x00";
            } else {
                $temp = '';
                $part = new BigInteger($part);
                while (!$part->equals($zero)) {
                    $submask = $part->bitwise_and($mask);
                    $submask->setPrecision(8);
                    $temp = (chr(0x80) | $submask->toBytes()) . $temp;
                    $part = $part->bitwise_rightShift(7);
                }
                $temp[strlen($temp) - 1] = $temp[strlen($temp) - 1] & chr(0x7f);
            }
            $value .= $temp;
        }
        return $value;
    }
    private static function decodeTime($content, $tag)
    {
        $format = 'YmdHis';
        if ($tag == self::TYPE_UTC_TIME) {
            if (preg_match('#^(\d{10})(Z|[+-]\d{4})$#', $content, $matches)) {
                $content = $matches[1] . '00' . $matches[2];
            }
            $prefix = (substr($content, 0, 2) >= 50) ? '19' : '20';
            $content = $prefix . $content;
        } elseif (strpos($content, '.') !== \false) {
            $format .= '.u';
        }
        if ($content[strlen($content) - 1] == 'Z') {
            $content = substr($content, 0, -1) . '+0000';
        }
        if (strpos($content, '-') !== \false || strpos($content, '+') !== \false) {
            $format .= 'O';
        }
        return @DateTime::createFromFormat($format, $content);
    }
    public static function setTimeFormat($format)
    {
        self::$format = $format;
    }
    /**
     * @param mixed[] $oids
     */
    public static function loadOIDs($oids)
    {
        self::$reverseOIDs += $oids;
        self::$oids = array_flip(self::$reverseOIDs);
    }
    /**
     * @param mixed[] $filters
     */
    public static function setFilters($filters)
    {
        self::$filters = $filters;
    }
    public static function convert($in, $from = self::TYPE_UTF8_STRING, $to = self::TYPE_UTF8_STRING)
    {
        if (!array_key_exists($from, self::STRING_TYPE_SIZE) || !array_key_exists($to, self::STRING_TYPE_SIZE)) {
            return \false;
        }
        $insize = self::STRING_TYPE_SIZE[$from];
        $outsize = self::STRING_TYPE_SIZE[$to];
        $inlength = strlen($in);
        $out = '';
        for ($i = 0; $i < $inlength;) {
            if ($inlength - $i < $insize) {
                return \false;
            }
            $c = ord($in[$i++]);
            switch (\true) {
                case $insize == 4:
                    $c = $c << 8 | ord($in[$i++]);
                    $c = $c << 8 | ord($in[$i++]);
                case $insize == 2:
                    $c = $c << 8 | ord($in[$i++]);
                case $insize == 1:
                    break;
                case ($c & 0x80) == 0x0:
                    break;
                case ($c & 0x40) == 0x0:
                    return \false;
                default:
                    $bit = 6;
                    do {
                        if ($bit > 25 || $i >= $inlength || (ord($in[$i]) & 0xc0) != 0x80) {
                            return \false;
                        }
                        $c = $c << 6 | ord($in[$i++]) & 0x3f;
                        $bit += 5;
                        $mask = 1 << $bit;
                    } while ($c & $bit);
                    $c &= $mask - 1;
                    break;
            }
            $v = '';
            switch (\true) {
                case $outsize == 4:
                    $v .= chr($c & 0xff);
                    $c >>= 8;
                    $v .= chr($c & 0xff);
                    $c >>= 8;
                case $outsize == 2:
                    $v .= chr($c & 0xff);
                    $c >>= 8;
                case $outsize == 1:
                    $v .= chr($c & 0xff);
                    $c >>= 8;
                    if ($c) {
                        return \false;
                    }
                    break;
                case ($c & ((\PHP_INT_SIZE == 8) ? 0x80000000 : (1 << 31))) != 0:
                    return \false;
                case $c >= 0x4000000:
                    $v .= chr(0x80 | $c & 0x3f);
                    $c = $c >> 6 | 0x4000000;
                case $c >= 0x200000:
                    $v .= chr(0x80 | $c & 0x3f);
                    $c = $c >> 6 | 0x200000;
                case $c >= 0x10000:
                    $v .= chr(0x80 | $c & 0x3f);
                    $c = $c >> 6 | 0x10000;
                case $c >= 0x800:
                    $v .= chr(0x80 | $c & 0x3f);
                    $c = $c >> 6 | 0x800;
                case $c >= 0x80:
                    $v .= chr(0x80 | $c & 0x3f);
                    $c = $c >> 6 | 0xc0;
                default:
                    $v .= chr($c);
                    break;
            }
            $out .= strrev($v);
        }
        return $out;
    }
    public static function extractBER($str)
    {
        if (strlen($str) > ini_get('pcre.backtrack_limit')) {
            $temp = $str;
        } else {
            $temp = preg_replace('#.*?^-+[^-]+-+[\r\n ]*$#ms', '', $str, 1);
            $temp = preg_replace('#-+END.*[\r\n ]*.*#ms', '', $temp, 1);
        }
        $temp = str_replace(["\r", "\n", ' '], '', $temp);
        $temp = preg_replace('#^-+[^-]+-+|-+[^-]+-+$#', '', $temp);
        $temp = preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $temp) ? Strings::base64_decode($temp) : \false;
        return ($temp != \false) ? $temp : $str;
    }
    public static function encodeLength($length)
    {
        if ($length <= 0x7f) {
            return chr($length);
        }
        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }
    public static function getOID($name)
    {
        return isset(self::$reverseOIDs[$name]) ? self::$reverseOIDs[$name] : $name;
    }
}
