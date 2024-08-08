<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use InvalidArgumentException;
final class Query
{
    public static function parse(string $str, $urlEncoding = \true): array
    {
        $result = [];
        if ($str === '') {
            return $result;
        }
        if ($urlEncoding === \true) {
            $decoder = function ($value) {
                return rawurldecode(str_replace('+', ' ', (string) $value));
            };
        } elseif ($urlEncoding === \PHP_QUERY_RFC3986) {
            $decoder = 'rawurldecode';
        } elseif ($urlEncoding === \PHP_QUERY_RFC1738) {
            $decoder = 'urldecode';
        } else {
            $decoder = function ($str) {
                return $str;
            };
        }
        foreach (explode('&', $str) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = $decoder($parts[0]);
            $value = isset($parts[1]) ? $decoder($parts[1]) : null;
            if (!array_key_exists($key, $result)) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }
        return $result;
    }
    public static function build(array $params, $encoding = \PHP_QUERY_RFC3986): string
    {
        if (!$params) {
            return '';
        }
        if ($encoding === \false) {
            $encoder = function (string $str): string {
                return $str;
            };
        } elseif ($encoding === \PHP_QUERY_RFC3986) {
            $encoder = 'rawurlencode';
        } elseif ($encoding === \PHP_QUERY_RFC1738) {
            $encoder = 'urlencode';
        } else {
            throw new InvalidArgumentException('Invalid type');
        }
        $qs = '';
        foreach ($params as $k => $v) {
            $k = $encoder((string) $k);
            if (!is_array($v)) {
                $qs .= $k;
                $v = is_bool($v) ? (int) $v : $v;
                if ($v !== null) {
                    $qs .= '=' . $encoder((string) $v);
                }
                $qs .= '&';
            } else {
                foreach ($v as $vv) {
                    $qs .= $k;
                    $vv = is_bool($vv) ? (int) $vv : $vv;
                    if ($vv !== null) {
                        $qs .= '=' . $encoder((string) $vv);
                    }
                    $qs .= '&';
                }
            }
        }
        return $qs ? (string) substr($qs, 0, -1) : '';
    }
}
