<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use LengthException;
use Staatic\Vendor\phpseclib3\Crypt\Common\StreamCipher;
class RC4 extends StreamCipher
{
    const ENCRYPT = 0;
    const DECRYPT = 1;
    protected $key_length = 128;
    protected $cipher_name_mcrypt = 'arcfour';
    protected $key;
    private $stream;
    protected function isValidEngineHelper($engine)
    {
        if ($engine == self::ENGINE_OPENSSL) {
            if ($this->continuousBuffer) {
                return \false;
            }
            if (defined('OPENSSL_VERSION_TEXT') && version_compare(preg_replace('#OpenSSL (\d+\.\d+\.\d+) .*#', '$1', \OPENSSL_VERSION_TEXT), '3.0.1', '>=')) {
                return \false;
            }
            $this->cipher_name_openssl = 'rc4-40';
        }
        return parent::isValidEngineHelper($engine);
    }
    public function setKeyLength($length)
    {
        if ($length < 8 || $length > 2048) {
            throw new LengthException('Key size of ' . $length . ' bits is not supported by this algorithm. Only keys between 1 and 256 bytes are supported');
        }
        $this->key_length = $length >> 3;
        parent::setKeyLength($length);
    }
    public function setKey($key)
    {
        $length = strlen($key);
        if ($length < 1 || $length > 256) {
            throw new LengthException('Key size of ' . $length . ' bytes is not supported by RC4. Keys must be between 1 and 256 bytes long');
        }
        parent::setKey($key);
    }
    public function encrypt($plaintext)
    {
        if ($this->engine != self::ENGINE_INTERNAL) {
            return parent::encrypt($plaintext);
        }
        return $this->crypt($plaintext, self::ENCRYPT);
    }
    public function decrypt($ciphertext)
    {
        if ($this->engine != self::ENGINE_INTERNAL) {
            return parent::decrypt($ciphertext);
        }
        return $this->crypt($ciphertext, self::DECRYPT);
    }
    protected function encryptBlock($in)
    {
    }
    protected function decryptBlock($in)
    {
    }
    protected function setupKey()
    {
        $key = $this->key;
        $keyLength = strlen($key);
        $keyStream = range(0, 255);
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = $j + $keyStream[$i] + ord($key[$i % $keyLength]) & 255;
            $temp = $keyStream[$i];
            $keyStream[$i] = $keyStream[$j];
            $keyStream[$j] = $temp;
        }
        $this->stream = [];
        $this->stream[self::DECRYPT] = $this->stream[self::ENCRYPT] = [0, 0, $keyStream];
    }
    private function crypt($text, $mode)
    {
        if ($this->changed) {
            $this->setup();
        }
        $stream =& $this->stream[$mode];
        if ($this->continuousBuffer) {
            $i =& $stream[0];
            $j =& $stream[1];
            $keyStream =& $stream[2];
        } else {
            $i = $stream[0];
            $j = $stream[1];
            $keyStream = $stream[2];
        }
        $len = strlen($text);
        for ($k = 0; $k < $len; ++$k) {
            $i = $i + 1 & 255;
            $ksi = $keyStream[$i];
            $j = $j + $ksi & 255;
            $ksj = $keyStream[$j];
            $keyStream[$i] = $ksj;
            $keyStream[$j] = $ksi;
            $text[$k] = $text[$k] ^ chr($keyStream[$ksj + $ksi & 255]);
        }
        return $text;
    }
}
