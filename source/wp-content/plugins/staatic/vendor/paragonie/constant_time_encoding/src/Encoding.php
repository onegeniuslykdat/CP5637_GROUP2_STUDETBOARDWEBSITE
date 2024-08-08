<?php

declare (strict_types=1);
namespace Staatic\Vendor\ParagonIE\ConstantTime;

use TypeError;
abstract class Encoding
{
    /**
     * @param string $str
     */
    public static function base32Encode($str): string
    {
        return Base32::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base32EncodeUpper($str): string
    {
        return Base32::encodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base32Decode($str): string
    {
        return Base32::decode($str);
    }
    /**
     * @param string $str
     */
    public static function base32DecodeUpper($str): string
    {
        return Base32::decodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base32HexEncode($str): string
    {
        return Base32Hex::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base32HexEncodeUpper($str): string
    {
        return Base32Hex::encodeUpper($str);
    }
    /**
     * @param string $str
     */
    public static function base32HexDecode($str): string
    {
        return Base32Hex::decode($str);
    }
    /**
     * @param string $str
     */
    public static function base32HexDecodeUpper($str): string
    {
        return Base32Hex::decodeUpper($str);
    }
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
        return Base64::decode($str);
    }
    /**
     * @param string $str
     */
    public static function base64EncodeDotSlash($str): string
    {
        return Base64DotSlash::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base64DecodeDotSlash($str): string
    {
        return Base64DotSlash::decode($str);
    }
    /**
     * @param string $str
     */
    public static function base64EncodeDotSlashOrdered($str): string
    {
        return Base64DotSlashOrdered::encode($str);
    }
    /**
     * @param string $str
     */
    public static function base64DecodeDotSlashOrdered($str): string
    {
        return Base64DotSlashOrdered::decode($str);
    }
    /**
     * @param string $bin_string
     */
    public static function hexEncode($bin_string): string
    {
        return Hex::encode($bin_string);
    }
    /**
     * @param string $hex_string
     */
    public static function hexDecode($hex_string): string
    {
        return Hex::decode($hex_string);
    }
    /**
     * @param string $bin_string
     */
    public static function hexEncodeUpper($bin_string): string
    {
        return Hex::encodeUpper($bin_string);
    }
    /**
     * @param string $bin_string
     */
    public static function hexDecodeUpper($bin_string): string
    {
        return Hex::decode($bin_string);
    }
}
