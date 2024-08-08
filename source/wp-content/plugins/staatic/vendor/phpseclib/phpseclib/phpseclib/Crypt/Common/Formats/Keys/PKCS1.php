<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\AES;
use Staatic\Vendor\phpseclib3\Crypt\DES;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Crypt\TripleDES;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\File\ASN1;
abstract class PKCS1 extends PKCS
{
    private static $defaultEncryptionAlgorithm = 'AES-128-CBC';
    public static function setEncryptionAlgorithm($algo)
    {
        self::$defaultEncryptionAlgorithm = $algo;
    }
    private static function getEncryptionMode($mode)
    {
        switch ($mode) {
            case 'CBC':
            case 'ECB':
            case 'CFB':
            case 'OFB':
            case 'CTR':
                return $mode;
        }
        throw new UnexpectedValueException('Unsupported block cipher mode of operation');
    }
    private static function getEncryptionObject($algo)
    {
        $modes = '(CBC|ECB|CFB|OFB|CTR)';
        switch (\true) {
            case preg_match("#^AES-(128|192|256)-{$modes}\$#", $algo, $matches):
                $cipher = new AES(self::getEncryptionMode($matches[2]));
                $cipher->setKeyLength($matches[1]);
                return $cipher;
            case preg_match("#^DES-EDE3-{$modes}\$#", $algo, $matches):
                return new TripleDES(self::getEncryptionMode($matches[1]));
            case preg_match("#^DES-{$modes}\$#", $algo, $matches):
                return new DES(self::getEncryptionMode($matches[1]));
            default:
                throw new UnsupportedAlgorithmException($algo . ' is not a supported algorithm');
        }
    }
    private static function generateSymmetricKey($password, $iv, $length)
    {
        $symkey = '';
        $iv = substr($iv, 0, 8);
        while (strlen($symkey) < $length) {
            $symkey .= md5($symkey . $password . $iv, \true);
        }
        return substr($symkey, 0, $length);
    }
    protected static function load($key, $password)
    {
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        if (preg_match('#DEK-Info: (.+),(.+)#', $key, $matches)) {
            $iv = Strings::hex2bin(trim($matches[2]));
            $key = preg_replace('#^(?:Proc-Type|DEK-Info): .*#m', '', $key);
            $ciphertext = ASN1::extractBER($key);
            if ($ciphertext === \false) {
                $ciphertext = $key;
            }
            $crypto = self::getEncryptionObject($matches[1]);
            $crypto->setKey(self::generateSymmetricKey($password, $iv, $crypto->getKeyLength() >> 3));
            $crypto->setIV($iv);
            $key = $crypto->decrypt($ciphertext);
        } else if (self::$format != self::MODE_DER) {
            $decoded = ASN1::extractBER($key);
            if ($decoded !== \false) {
                $key = $decoded;
            } elseif (self::$format == self::MODE_PEM) {
                throw new UnexpectedValueException('Expected base64-encoded PEM format but was unable to decode base64 text');
            }
        }
        return $key;
    }
    /**
     * @param mixed[] $options
     */
    protected static function wrapPrivateKey($key, $type, $password, $options = [])
    {
        if (empty($password) || !is_string($password)) {
            return "-----BEGIN {$type} PRIVATE KEY-----\r\n" . chunk_split(Strings::base64_encode($key), 64) . "-----END {$type} PRIVATE KEY-----";
        }
        $encryptionAlgorithm = isset($options['encryptionAlgorithm']) ? $options['encryptionAlgorithm'] : self::$defaultEncryptionAlgorithm;
        $cipher = self::getEncryptionObject($encryptionAlgorithm);
        $iv = Random::string($cipher->getBlockLength() >> 3);
        $cipher->setKey(self::generateSymmetricKey($password, $iv, $cipher->getKeyLength() >> 3));
        $cipher->setIV($iv);
        $iv = strtoupper(Strings::bin2hex($iv));
        return "-----BEGIN {$type} PRIVATE KEY-----\r\n" . "Proc-Type: 4,ENCRYPTED\r\n" . "DEK-Info: " . $encryptionAlgorithm . ",{$iv}\r\n" . "\r\n" . chunk_split(Strings::base64_encode($cipher->encrypt($key)), 64) . "-----END {$type} PRIVATE KEY-----";
    }
    protected static function wrapPublicKey($key, $type)
    {
        return "-----BEGIN {$type} PUBLIC KEY-----\r\n" . chunk_split(Strings::base64_encode($key), 64) . "-----END {$type} PUBLIC KEY-----";
    }
}
