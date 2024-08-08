<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

use TypeError;
abstract class RFC4648
{
    /**
     * @param string $str
     */
    public static function base64Encode($str): string
    {
        return Base64::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base64Decode($str): string
    {
        return Base64::decode($str, \true);
    }
    /**
     * @param string $str
     */
    public static function base64UrlSafeEncode($str): string
    {
        return Base64UrlSafe::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base64UrlSafeDecode($str): string
    {
        return Base64UrlSafe::decode($str, \true);
    }
    /**
     * @param string $str
     */
    public static function base32Encode($str): string
    {
        return Base32::encodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base32Decode($str): string
    {
        return Base32::decodeUpper($str, \true);
    }
    /**
     * @param string $str
     */
    public static function base32HexEncode($str): string
    {
        return Base32::encodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base32HexDecode($str): string
    {
        return Base32::decodeUpper($str, \true);
    }
    /**
     * @param string $str
     */
    public static function base16Encode($str): string
    {
        return Hex::encodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base16Decode($str): string
    {
        return Hex::decode($str, \true);
    }
}
