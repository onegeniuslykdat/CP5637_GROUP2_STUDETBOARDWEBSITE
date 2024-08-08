<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common;

use BadMethodCallException;
use LengthException;
use RuntimeException;
use Closure;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Blowfish;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Exception\BadDecryptionException;
use Staatic\Vendor\phpseclib3\Exception\BadModeException;
use Staatic\Vendor\phpseclib3\Exception\InconsistentSetupException;
use Staatic\Vendor\phpseclib3\Exception\InsufficientSetupException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\BinaryField;
use Staatic\Vendor\phpseclib3\Math\PrimeField;
abstract class SymmetricKey
{
    const MODE_CTR = -1;
    const MODE_ECB = 1;
    const MODE_CBC = 2;
    const MODE_CFB = 3;
    const MODE_CFB8 = 7;
    const MODE_OFB8 = 8;
    const MODE_OFB = 4;
    const MODE_GCM = 5;
    const MODE_STREAM = 6;
    const MODE_MAP = ['ctr' => self::MODE_CTR, 'ecb' => self::MODE_ECB, 'cbc' => self::MODE_CBC, 'cfb' => self::MODE_CFB, 'cfb8' => self::MODE_CFB8, 'ofb' => self::MODE_OFB, 'ofb8' => self::MODE_OFB8, 'gcm' => self::MODE_GCM, 'stream' => self::MODE_STREAM];
    const ENGINE_INTERNAL = 1;
    const ENGINE_EVAL = 2;
    const ENGINE_MCRYPT = 3;
    const ENGINE_OPENSSL = 4;
    const ENGINE_LIBSODIUM = 5;
    const ENGINE_OPENSSL_GCM = 6;
    const ENGINE_MAP = [self::ENGINE_INTERNAL => 'PHP', self::ENGINE_EVAL => 'Eval', self::ENGINE_MCRYPT => 'mcrypt', self::ENGINE_OPENSSL => 'OpenSSL', self::ENGINE_LIBSODIUM => 'libsodium', self::ENGINE_OPENSSL_GCM => 'OpenSSL (GCM)'];
    protected $mode;
    protected $block_size = 16;
    protected $key = \false;
    protected $hKey = \false;
    protected $iv = \false;
    protected $encryptIV;
    protected $decryptIV;
    protected $continuousBuffer = \false;
    protected $enbuffer;
    protected $debuffer;
    private $enmcrypt;
    private $demcrypt;
    private $enchanged = \true;
    private $dechanged = \true;
    private $ecb;
    protected $cfb_init_len = 600;
    protected $changed = \true;
    protected $nonIVChanged = \true;
    private $padding = \true;
    private $paddable = \false;
    protected $engine;
    private $preferredEngine;
    protected $cipher_name_mcrypt;
    protected $cipher_name_openssl;
    protected $cipher_name_openssl_ecb;
    private $password_default_salt = 'phpseclib/salt';
    protected $inline_crypt;
    private $openssl_emulate_ctr = \false;
    private $skip_key_adjustment = \false;
    protected $explicit_key_length = \false;
    private $h;
    protected $aad = '';
    protected $newtag = \false;
    protected $oldtag = \false;
    private static $gcmField;
    private static $poly1305Field;
    protected static $use_reg_intval;
    protected $poly1305Key;
    protected $usePoly1305 = \false;
    private $origIV = \false;
    protected $nonce = \false;
    public function __construct($mode)
    {
        $mode = strtolower($mode);
        $map = self::MODE_MAP;
        if (!isset($map[$mode])) {
            throw new BadModeException('No valid mode has been specified');
        }
        $mode = self::MODE_MAP[$mode];
        switch ($mode) {
            case self::MODE_ECB:
            case self::MODE_CBC:
                $this->paddable = \true;
                break;
            case self::MODE_CTR:
            case self::MODE_CFB:
            case self::MODE_CFB8:
            case self::MODE_OFB:
            case self::MODE_OFB8:
            case self::MODE_STREAM:
                $this->paddable = \false;
                break;
            case self::MODE_GCM:
                if ($this->block_size != 16) {
                    throw new BadModeException('GCM is only valid for block ciphers with a block size of 128 bits');
                }
                if (!isset(self::$gcmField)) {
                    self::$gcmField = new BinaryField(128, 7, 2, 1, 0);
                }
                $this->paddable = \false;
                break;
            default:
                throw new BadModeException('No valid mode has been specified');
        }
        $this->mode = $mode;
        static::initialize_static_variables();
    }
    protected static function initialize_static_variables()
    {
        if (!isset(self::$use_reg_intval)) {
            switch (\true) {
                case (\PHP_OS & "\xdf\xdf\xdf") === 'WIN':
                case !function_exists('php_uname'):
                case !is_string(php_uname('m')):
                case (php_uname('m') & "\xdf\xdf\xdf") != 'ARM':
                case defined('PHP_INT_SIZE') && \PHP_INT_SIZE == 8:
                    self::$use_reg_intval = \true;
                    break;
                case (php_uname('m') & "\xdf\xdf\xdf") == 'ARM':
                    switch (\true) {
                        case \PHP_VERSION_ID >= 70000 && \PHP_VERSION_ID <= 70123:
                        case \PHP_VERSION_ID >= 70200 && \PHP_VERSION_ID <= 70211:
                            self::$use_reg_intval = \false;
                            break;
                        default:
                            self::$use_reg_intval = \true;
                    }
            }
        }
    }
    public function setIV($iv)
    {
        if ($this->mode == self::MODE_ECB) {
            throw new BadMethodCallException('This mode does not require an IV.');
        }
        if ($this->mode == self::MODE_GCM) {
            throw new BadMethodCallException('Use setNonce instead');
        }
        if (!$this->usesIV()) {
            throw new BadMethodCallException('This algorithm does not use an IV.');
        }
        if (strlen($iv) != $this->block_size) {
            throw new LengthException('Received initialization vector of size ' . strlen($iv) . ', but size ' . $this->block_size . ' is required');
        }
        $this->iv = $this->origIV = $iv;
        $this->changed = \true;
    }
    public function enablePoly1305()
    {
        if ($this->mode == self::MODE_GCM) {
            throw new BadMethodCallException('Poly1305 cannot be used in GCM mode');
        }
        $this->usePoly1305 = \true;
    }
    public function setPoly1305Key($key = null)
    {
        if ($this->mode == self::MODE_GCM) {
            throw new BadMethodCallException('Poly1305 cannot be used in GCM mode');
        }
        if (!is_string($key) || strlen($key) != 32) {
            throw new LengthException('The Poly1305 key must be 32 bytes long (256 bits)');
        }
        if (!isset(self::$poly1305Field)) {
            self::$poly1305Field = new PrimeField(new BigInteger('3fffffffffffffffffffffffffffffffb', 16));
        }
        $this->poly1305Key = $key;
        $this->usePoly1305 = \true;
    }
    public function setNonce($nonce)
    {
        if ($this->mode != self::MODE_GCM) {
            throw new BadMethodCallException('Nonces are only used in GCM mode.');
        }
        $this->nonce = $nonce;
        $this->setEngine();
    }
    public function setAAD($aad)
    {
        if ($this->mode != self::MODE_GCM && !$this->usePoly1305) {
            throw new BadMethodCallException('Additional authenticated data is only utilized in GCM mode or with Poly1305');
        }
        $this->aad = $aad;
    }
    public function usesIV()
    {
        return $this->mode != self::MODE_GCM && $this->mode != self::MODE_ECB;
    }
    public function usesNonce()
    {
        return $this->mode == self::MODE_GCM;
    }
    public function getKeyLength()
    {
        return $this->key_length << 3;
    }
    public function getBlockLength()
    {
        return $this->block_size << 3;
    }
    public function getBlockLengthInBytes()
    {
        return $this->block_size;
    }
    public function setKeyLength($length)
    {
        $this->explicit_key_length = $length >> 3;
        if (is_string($this->key) && strlen($this->key) != $this->explicit_key_length) {
            $this->key = \false;
            throw new InconsistentSetupException('Key has already been set and is not ' . $this->explicit_key_length . ' bytes long');
        }
    }
    public function setKey($key)
    {
        if ($this->explicit_key_length !== \false && strlen($key) != $this->explicit_key_length) {
            throw new InconsistentSetupException('Key length has already been set to ' . $this->explicit_key_length . ' bytes and this key is ' . strlen($key) . ' bytes');
        }
        $this->key = $key;
        $this->key_length = strlen($key);
        $this->setEngine();
    }
    public function setPassword($password, $method = 'pbkdf2', ...$func_args)
    {
        $key = '';
        $method = strtolower($method);
        switch ($method) {
            case 'bcrypt':
                if (!isset($func_args[2])) {
                    throw new RuntimeException('A salt must be provided for bcrypt to work');
                }
                $salt = $func_args[0];
                $rounds = isset($func_args[1]) ? $func_args[1] : 16;
                $keylen = isset($func_args[2]) ? $func_args[2] : $this->key_length;
                $key = Blowfish::bcrypt_pbkdf($password, $salt, $keylen + $this->block_size, $rounds);
                $this->setKey(substr($key, 0, $keylen));
                $this->setIV(substr($key, $keylen));
                return \true;
            case 'pkcs12':
            case 'pbkdf1':
            case 'pbkdf2':
                $hash = isset($func_args[0]) ? strtolower($func_args[0]) : 'sha1';
                $hashObj = new Hash();
                $hashObj->setHash($hash);
                $salt = isset($func_args[1]) ? $func_args[1] : $this->password_default_salt;
                $count = isset($func_args[2]) ? $func_args[2] : 1000;
                if (isset($func_args[3])) {
                    if ($func_args[3] <= 0) {
                        throw new LengthException('Derived key length cannot be longer 0 or less');
                    }
                    $dkLen = $func_args[3];
                } else {
                    $key_length = ($this->explicit_key_length !== \false) ? $this->explicit_key_length : $this->key_length;
                    $dkLen = ($method == 'pbkdf1') ? 2 * $key_length : $key_length;
                }
                switch (\true) {
                    case $method == 'pkcs12':
                        $password = "\x00" . chunk_split($password, 1, "\x00") . "\x00";
                        $blockLength = $hashObj->getBlockLengthInBytes();
                        $d1 = str_repeat(chr(1), $blockLength);
                        $d2 = str_repeat(chr(2), $blockLength);
                        $s = '';
                        if (strlen($salt)) {
                            while (strlen($s) < $blockLength) {
                                $s .= $salt;
                            }
                        }
                        $s = substr($s, 0, $blockLength);
                        $p = '';
                        if (strlen($password)) {
                            while (strlen($p) < $blockLength) {
                                $p .= $password;
                            }
                        }
                        $p = substr($p, 0, $blockLength);
                        $i = $s . $p;
                        $this->setKey(self::pkcs12helper($dkLen, $hashObj, $i, $d1, $count));
                        if ($this->usesIV()) {
                            $this->setIV(self::pkcs12helper($this->block_size, $hashObj, $i, $d2, $count));
                        }
                        return \true;
                    case $method == 'pbkdf1':
                        if ($dkLen > $hashObj->getLengthInBytes()) {
                            throw new LengthException('Derived key length cannot be longer than the hash length');
                        }
                        $t = $password . $salt;
                        for ($i = 0; $i < $count; ++$i) {
                            $t = $hashObj->hash($t);
                        }
                        $key = substr($t, 0, $dkLen);
                        $this->setKey(substr($key, 0, $dkLen >> 1));
                        if ($this->usesIV()) {
                            $this->setIV(substr($key, $dkLen >> 1));
                        }
                        return \true;
                    case !in_array($hash, hash_algos()):
                        $i = 1;
                        $hashObj->setKey($password);
                        while (strlen($key) < $dkLen) {
                            $f = $u = $hashObj->hash($salt . pack('N', $i++));
                            for ($j = 2; $j <= $count; ++$j) {
                                $u = $hashObj->hash($u);
                                $f ^= $u;
                            }
                            $key .= $f;
                        }
                        $key = substr($key, 0, $dkLen);
                        break;
                    default:
                        $key = hash_pbkdf2($hash, $password, $salt, $count, $dkLen, \true);
                }
                break;
            default:
                throw new UnsupportedAlgorithmException($method . ' is not a supported password hashing method');
        }
        $this->setKey($key);
        return \true;
    }
    private static function pkcs12helper($n, $hashObj, $i, $d, $count)
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }
        $blockLength = $hashObj->getBlockLength() >> 3;
        $c = ceil($n / $hashObj->getLengthInBytes());
        $a = '';
        for ($j = 1; $j <= $c; $j++) {
            $ai = $d . $i;
            for ($k = 0; $k < $count; $k++) {
                $ai = $hashObj->hash($ai);
            }
            $b = '';
            while (strlen($b) < $blockLength) {
                $b .= $ai;
            }
            $b = substr($b, 0, $blockLength);
            $b = new BigInteger($b, 256);
            $newi = '';
            for ($k = 0; $k < strlen($i); $k += $blockLength) {
                $temp = substr($i, $k, $blockLength);
                $temp = new BigInteger($temp, 256);
                $temp->setPrecision($blockLength << 3);
                $temp = $temp->add($b);
                $temp = $temp->add($one);
                $newi .= $temp->toBytes(\false);
            }
            $i = $newi;
            $a .= $ai;
        }
        return substr($a, 0, $n);
    }
    public function encrypt($plaintext)
    {
        if ($this->paddable) {
            $plaintext = $this->pad($plaintext);
        }
        $this->setup();
        if ($this->mode == self::MODE_GCM) {
            $oldIV = $this->iv;
            Strings::increment_str($this->iv);
            $cipher = new static('ctr');
            $cipher->setKey($this->key);
            $cipher->setIV($this->iv);
            $ciphertext = $cipher->encrypt($plaintext);
            $s = $this->ghash(self::nullPad128($this->aad) . self::nullPad128($ciphertext) . self::len64($this->aad) . self::len64($ciphertext));
            $cipher->encryptIV = $this->iv = $this->encryptIV = $this->decryptIV = $oldIV;
            $this->newtag = $cipher->encrypt($s);
            return $ciphertext;
        }
        if (isset($this->poly1305Key)) {
            $cipher = clone $this;
            unset($cipher->poly1305Key);
            $this->usePoly1305 = \false;
            $ciphertext = $cipher->encrypt($plaintext);
            $this->newtag = $this->poly1305($ciphertext);
            return $ciphertext;
        }
        if ($this->engine === self::ENGINE_OPENSSL) {
            switch ($this->mode) {
                case self::MODE_STREAM:
                    return openssl_encrypt($plaintext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                case self::MODE_ECB:
                    return openssl_encrypt($plaintext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                case self::MODE_CBC:
                    $result = openssl_encrypt($plaintext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $this->encryptIV);
                    if ($this->continuousBuffer) {
                        $this->encryptIV = substr($result, -$this->block_size);
                    }
                    return $result;
                case self::MODE_CTR:
                    return $this->openssl_ctr_process($plaintext, $this->encryptIV, $this->enbuffer);
                case self::MODE_CFB:
                    $ciphertext = '';
                    if ($this->continuousBuffer) {
                        $iv =& $this->encryptIV;
                        $pos =& $this->enbuffer['pos'];
                    } else {
                        $iv = $this->encryptIV;
                        $pos = 0;
                    }
                    $len = strlen($plaintext);
                    $i = 0;
                    if ($pos) {
                        $orig_pos = $pos;
                        $max = $this->block_size - $pos;
                        if ($len >= $max) {
                            $i = $max;
                            $len -= $max;
                            $pos = 0;
                        } else {
                            $i = $len;
                            $pos += $len;
                            $len = 0;
                        }
                        $ciphertext = substr($iv, $orig_pos) ^ $plaintext;
                        $iv = substr_replace($iv, $ciphertext, $orig_pos, $i);
                        $plaintext = substr($plaintext, $i);
                    }
                    $overflow = $len % $this->block_size;
                    if ($overflow) {
                        $ciphertext .= openssl_encrypt(substr($plaintext, 0, -$overflow) . str_repeat("\x00", $this->block_size), $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $iv);
                        $iv = Strings::pop($ciphertext, $this->block_size);
                        $size = $len - $overflow;
                        $block = $iv ^ substr($plaintext, -$overflow);
                        $iv = substr_replace($iv, $block, 0, $overflow);
                        $ciphertext .= $block;
                        $pos = $overflow;
                    } elseif ($len) {
                        $ciphertext = openssl_encrypt($plaintext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $iv);
                        $iv = substr($ciphertext, -$this->block_size);
                    }
                    return $ciphertext;
                case self::MODE_CFB8:
                    $ciphertext = openssl_encrypt($plaintext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $this->encryptIV);
                    if ($this->continuousBuffer) {
                        if (($len = strlen($ciphertext)) >= $this->block_size) {
                            $this->encryptIV = substr($ciphertext, -$this->block_size);
                        } else {
                            $this->encryptIV = substr($this->encryptIV, $len - $this->block_size) . substr($ciphertext, -$len);
                        }
                    }
                    return $ciphertext;
                case self::MODE_OFB8:
                    $ciphertext = '';
                    $len = strlen($plaintext);
                    $iv = $this->encryptIV;
                    for ($i = 0; $i < $len; ++$i) {
                        $xor = openssl_encrypt($iv, $this->cipher_name_openssl_ecb, $this->key, $this->openssl_options, $this->decryptIV);
                        $ciphertext .= $plaintext[$i] ^ $xor;
                        $iv = substr($iv, 1) . $xor[0];
                    }
                    if ($this->continuousBuffer) {
                        $this->encryptIV = $iv;
                    }
                    break;
                case self::MODE_OFB:
                    return $this->openssl_ofb_process($plaintext, $this->encryptIV, $this->enbuffer);
            }
        }
        if ($this->engine === self::ENGINE_MCRYPT) {
            set_error_handler(function () {
            });
            if ($this->enchanged) {
                mcrypt_generic_init($this->enmcrypt, $this->key, $this->getIV($this->encryptIV));
                $this->enchanged = \false;
            }
            if ($this->mode == self::MODE_CFB && $this->continuousBuffer) {
                $block_size = $this->block_size;
                $iv =& $this->encryptIV;
                $pos =& $this->enbuffer['pos'];
                $len = strlen($plaintext);
                $ciphertext = '';
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = $block_size - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len -= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos += $len;
                        $len = 0;
                    }
                    $ciphertext = substr($iv, $orig_pos) ^ $plaintext;
                    $iv = substr_replace($iv, $ciphertext, $orig_pos, $i);
                    $this->enbuffer['enmcrypt_init'] = \true;
                }
                if ($len >= $block_size) {
                    if ($this->enbuffer['enmcrypt_init'] === \false || $len > $this->cfb_init_len) {
                        if ($this->enbuffer['enmcrypt_init'] === \true) {
                            mcrypt_generic_init($this->enmcrypt, $this->key, $iv);
                            $this->enbuffer['enmcrypt_init'] = \false;
                        }
                        $ciphertext .= mcrypt_generic($this->enmcrypt, substr($plaintext, $i, $len - $len % $block_size));
                        $iv = substr($ciphertext, -$block_size);
                        $len %= $block_size;
                    } else {
                        while ($len >= $block_size) {
                            $iv = mcrypt_generic($this->ecb, $iv) ^ substr($plaintext, $i, $block_size);
                            $ciphertext .= $iv;
                            $len -= $block_size;
                            $i += $block_size;
                        }
                    }
                }
                if ($len) {
                    $iv = mcrypt_generic($this->ecb, $iv);
                    $block = $iv ^ substr($plaintext, -$len);
                    $iv = substr_replace($iv, $block, 0, $len);
                    $ciphertext .= $block;
                    $pos = $len;
                }
                restore_error_handler();
                return $ciphertext;
            }
            $ciphertext = mcrypt_generic($this->enmcrypt, $plaintext);
            if (!$this->continuousBuffer) {
                mcrypt_generic_init($this->enmcrypt, $this->key, $this->getIV($this->encryptIV));
            }
            restore_error_handler();
            return $ciphertext;
        }
        if ($this->engine === self::ENGINE_EVAL) {
            $inline = $this->inline_crypt;
            return $inline('encrypt', $plaintext);
        }
        $buffer =& $this->enbuffer;
        $block_size = $this->block_size;
        $ciphertext = '';
        switch ($this->mode) {
            case self::MODE_ECB:
                for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                    $ciphertext .= $this->encryptBlock(substr($plaintext, $i, $block_size));
                }
                break;
            case self::MODE_CBC:
                $xor = $this->encryptIV;
                for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                    $block = substr($plaintext, $i, $block_size);
                    $block = $this->encryptBlock($block ^ $xor);
                    $xor = $block;
                    $ciphertext .= $block;
                }
                if ($this->continuousBuffer) {
                    $this->encryptIV = $xor;
                }
                break;
            case self::MODE_CTR:
                $xor = $this->encryptIV;
                if (strlen($buffer['ciphertext'])) {
                    for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                        $block = substr($plaintext, $i, $block_size);
                        if (strlen($block) > strlen($buffer['ciphertext'])) {
                            $buffer['ciphertext'] .= $this->encryptBlock($xor);
                            Strings::increment_str($xor);
                        }
                        $key = Strings::shift($buffer['ciphertext'], $block_size);
                        $ciphertext .= $block ^ $key;
                    }
                } else {
                    for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                        $block = substr($plaintext, $i, $block_size);
                        $key = $this->encryptBlock($xor);
                        Strings::increment_str($xor);
                        $ciphertext .= $block ^ $key;
                    }
                }
                if ($this->continuousBuffer) {
                    $this->encryptIV = $xor;
                    if ($start = strlen($plaintext) % $block_size) {
                        $buffer['ciphertext'] = substr($key, $start) . $buffer['ciphertext'];
                    }
                }
                break;
            case self::MODE_CFB:
                if ($this->continuousBuffer) {
                    $iv =& $this->encryptIV;
                    $pos =& $buffer['pos'];
                } else {
                    $iv = $this->encryptIV;
                    $pos = 0;
                }
                $len = strlen($plaintext);
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = $block_size - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len -= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos += $len;
                        $len = 0;
                    }
                    $ciphertext = substr($iv, $orig_pos) ^ $plaintext;
                    $iv = substr_replace($iv, $ciphertext, $orig_pos, $i);
                }
                while ($len >= $block_size) {
                    $iv = $this->encryptBlock($iv) ^ substr($plaintext, $i, $block_size);
                    $ciphertext .= $iv;
                    $len -= $block_size;
                    $i += $block_size;
                }
                if ($len) {
                    $iv = $this->encryptBlock($iv);
                    $block = $iv ^ substr($plaintext, $i);
                    $iv = substr_replace($iv, $block, 0, $len);
                    $ciphertext .= $block;
                    $pos = $len;
                }
                break;
            case self::MODE_CFB8:
                $ciphertext = '';
                $len = strlen($plaintext);
                $iv = $this->encryptIV;
                for ($i = 0; $i < $len; ++$i) {
                    $ciphertext .= $c = $plaintext[$i] ^ $this->encryptBlock($iv);
                    $iv = substr($iv, 1) . $c;
                }
                if ($this->continuousBuffer) {
                    if ($len >= $block_size) {
                        $this->encryptIV = substr($ciphertext, -$block_size);
                    } else {
                        $this->encryptIV = substr($this->encryptIV, $len - $block_size) . substr($ciphertext, -$len);
                    }
                }
                break;
            case self::MODE_OFB8:
                $ciphertext = '';
                $len = strlen($plaintext);
                $iv = $this->encryptIV;
                for ($i = 0; $i < $len; ++$i) {
                    $xor = $this->encryptBlock($iv);
                    $ciphertext .= $plaintext[$i] ^ $xor;
                    $iv = substr($iv, 1) . $xor[0];
                }
                if ($this->continuousBuffer) {
                    $this->encryptIV = $iv;
                }
                break;
            case self::MODE_OFB:
                $xor = $this->encryptIV;
                if (strlen($buffer['xor'])) {
                    for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                        $block = substr($plaintext, $i, $block_size);
                        if (strlen($block) > strlen($buffer['xor'])) {
                            $xor = $this->encryptBlock($xor);
                            $buffer['xor'] .= $xor;
                        }
                        $key = Strings::shift($buffer['xor'], $block_size);
                        $ciphertext .= $block ^ $key;
                    }
                } else {
                    for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                        $xor = $this->encryptBlock($xor);
                        $ciphertext .= substr($plaintext, $i, $block_size) ^ $xor;
                    }
                    $key = $xor;
                }
                if ($this->continuousBuffer) {
                    $this->encryptIV = $xor;
                    if ($start = strlen($plaintext) % $block_size) {
                        $buffer['xor'] = substr($key, $start) . $buffer['xor'];
                    }
                }
                break;
            case self::MODE_STREAM:
                $ciphertext = $this->encryptBlock($plaintext);
                break;
        }
        return $ciphertext;
    }
    public function decrypt($ciphertext)
    {
        if ($this->paddable && strlen($ciphertext) % $this->block_size) {
            throw new LengthException('The ciphertext length (' . strlen($ciphertext) . ') needs to be a multiple of the block size (' . $this->block_size . ')');
        }
        $this->setup();
        if ($this->mode == self::MODE_GCM || isset($this->poly1305Key)) {
            if ($this->oldtag === \false) {
                throw new InsufficientSetupException('Authentication Tag has not been set');
            }
            if (isset($this->poly1305Key)) {
                $newtag = $this->poly1305($ciphertext);
            } else {
                $oldIV = $this->iv;
                Strings::increment_str($this->iv);
                $cipher = new static('ctr');
                $cipher->setKey($this->key);
                $cipher->setIV($this->iv);
                $plaintext = $cipher->decrypt($ciphertext);
                $s = $this->ghash(self::nullPad128($this->aad) . self::nullPad128($ciphertext) . self::len64($this->aad) . self::len64($ciphertext));
                $cipher->encryptIV = $this->iv = $this->encryptIV = $this->decryptIV = $oldIV;
                $newtag = $cipher->encrypt($s);
            }
            if ($this->oldtag != substr($newtag, 0, strlen($newtag))) {
                $cipher = clone $this;
                unset($cipher->poly1305Key);
                $this->usePoly1305 = \false;
                $plaintext = $cipher->decrypt($ciphertext);
                $this->oldtag = \false;
                throw new BadDecryptionException('Derived authentication tag and supplied authentication tag do not match');
            }
            $this->oldtag = \false;
            return $plaintext;
        }
        if ($this->engine === self::ENGINE_OPENSSL) {
            switch ($this->mode) {
                case self::MODE_STREAM:
                    $plaintext = openssl_decrypt($ciphertext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                    break;
                case self::MODE_ECB:
                    $plaintext = openssl_decrypt($ciphertext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                    break;
                case self::MODE_CBC:
                    $offset = $this->block_size;
                    $plaintext = openssl_decrypt($ciphertext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $this->decryptIV);
                    if ($this->continuousBuffer) {
                        $this->decryptIV = substr($ciphertext, -$offset, $this->block_size);
                    }
                    break;
                case self::MODE_CTR:
                    $plaintext = $this->openssl_ctr_process($ciphertext, $this->decryptIV, $this->debuffer);
                    break;
                case self::MODE_CFB:
                    $plaintext = '';
                    if ($this->continuousBuffer) {
                        $iv =& $this->decryptIV;
                        $pos =& $this->debuffer['pos'];
                    } else {
                        $iv = $this->decryptIV;
                        $pos = 0;
                    }
                    $len = strlen($ciphertext);
                    $i = 0;
                    if ($pos) {
                        $orig_pos = $pos;
                        $max = $this->block_size - $pos;
                        if ($len >= $max) {
                            $i = $max;
                            $len -= $max;
                            $pos = 0;
                        } else {
                            $i = $len;
                            $pos += $len;
                            $len = 0;
                        }
                        $plaintext = substr($iv, $orig_pos) ^ $ciphertext;
                        $iv = substr_replace($iv, substr($ciphertext, 0, $i), $orig_pos, $i);
                        $ciphertext = substr($ciphertext, $i);
                    }
                    $overflow = $len % $this->block_size;
                    if ($overflow) {
                        $plaintext .= openssl_decrypt(substr($ciphertext, 0, -$overflow), $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $iv);
                        if ($len - $overflow) {
                            $iv = substr($ciphertext, -$overflow - $this->block_size, -$overflow);
                        }
                        $iv = openssl_encrypt(str_repeat("\x00", $this->block_size), $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $iv);
                        $plaintext .= $iv ^ substr($ciphertext, -$overflow);
                        $iv = substr_replace($iv, substr($ciphertext, -$overflow), 0, $overflow);
                        $pos = $overflow;
                    } elseif ($len) {
                        $plaintext .= openssl_decrypt($ciphertext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $iv);
                        $iv = substr($ciphertext, -$this->block_size);
                    }
                    break;
                case self::MODE_CFB8:
                    $plaintext = openssl_decrypt($ciphertext, $this->cipher_name_openssl, $this->key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $this->decryptIV);
                    if ($this->continuousBuffer) {
                        if (($len = strlen($ciphertext)) >= $this->block_size) {
                            $this->decryptIV = substr($ciphertext, -$this->block_size);
                        } else {
                            $this->decryptIV = substr($this->decryptIV, $len - $this->block_size) . substr($ciphertext, -$len);
                        }
                    }
                    break;
                case self::MODE_OFB8:
                    $plaintext = '';
                    $len = strlen($ciphertext);
                    $iv = $this->decryptIV;
                    for ($i = 0; $i < $len; ++$i) {
                        $xor = openssl_encrypt($iv, $this->cipher_name_openssl_ecb, $this->key, $this->openssl_options, $this->decryptIV);
                        $plaintext .= $ciphertext[$i] ^ $xor;
                        $iv = substr($iv, 1) . $xor[0];
                    }
                    if ($this->continuousBuffer) {
                        $this->decryptIV = $iv;
                    }
                    break;
                case self::MODE_OFB:
                    $plaintext = $this->openssl_ofb_process($ciphertext, $this->decryptIV, $this->debuffer);
            }
            return $this->paddable ? $this->unpad($plaintext) : $plaintext;
        }
        if ($this->engine === self::ENGINE_MCRYPT) {
            set_error_handler(function () {
            });
            $block_size = $this->block_size;
            if ($this->dechanged) {
                mcrypt_generic_init($this->demcrypt, $this->key, $this->getIV($this->decryptIV));
                $this->dechanged = \false;
            }
            if ($this->mode == self::MODE_CFB && $this->continuousBuffer) {
                $iv =& $this->decryptIV;
                $pos =& $this->debuffer['pos'];
                $len = strlen($ciphertext);
                $plaintext = '';
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = $block_size - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len -= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos += $len;
                        $len = 0;
                    }
                    $plaintext = substr($iv, $orig_pos) ^ $ciphertext;
                    $iv = substr_replace($iv, substr($ciphertext, 0, $i), $orig_pos, $i);
                }
                if ($len >= $block_size) {
                    $cb = substr($ciphertext, $i, $len - $len % $block_size);
                    $plaintext .= mcrypt_generic($this->ecb, $iv . $cb) ^ $cb;
                    $iv = substr($cb, -$block_size);
                    $len %= $block_size;
                }
                if ($len) {
                    $iv = mcrypt_generic($this->ecb, $iv);
                    $plaintext .= $iv ^ substr($ciphertext, -$len);
                    $iv = substr_replace($iv, substr($ciphertext, -$len), 0, $len);
                    $pos = $len;
                }
                restore_error_handler();
                return $plaintext;
            }
            $plaintext = mdecrypt_generic($this->demcrypt, $ciphertext);
            if (!$this->continuousBuffer) {
                mcrypt_generic_init($this->demcrypt, $this->key, $this->getIV($this->decryptIV));
            }
            restore_error_handler();
            return $this->paddable ? $this->unpad($plaintext) : $plaintext;
        }
        if ($this->engine === self::ENGINE_EVAL) {
            $inline = $this->inline_crypt;
            return $inline('decrypt', $ciphertext);
        }
        $block_size = $this->block_size;
        $buffer =& $this->debuffer;
        $plaintext = '';
        switch ($this->mode) {
            case self::MODE_ECB:
                for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                    $plaintext .= $this->decryptBlock(substr($ciphertext, $i, $block_size));
                }
                break;
            case self::MODE_CBC:
                $xor = $this->decryptIV;
                for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                    $block = substr($ciphertext, $i, $block_size);
                    $plaintext .= $this->decryptBlock($block) ^ $xor;
                    $xor = $block;
                }
                if ($this->continuousBuffer) {
                    $this->decryptIV = $xor;
                }
                break;
            case self::MODE_CTR:
                $xor = $this->decryptIV;
                if (strlen($buffer['ciphertext'])) {
                    for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                        $block = substr($ciphertext, $i, $block_size);
                        if (strlen($block) > strlen($buffer['ciphertext'])) {
                            $buffer['ciphertext'] .= $this->encryptBlock($xor);
                            Strings::increment_str($xor);
                        }
                        $key = Strings::shift($buffer['ciphertext'], $block_size);
                        $plaintext .= $block ^ $key;
                    }
                } else {
                    for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                        $block = substr($ciphertext, $i, $block_size);
                        $key = $this->encryptBlock($xor);
                        Strings::increment_str($xor);
                        $plaintext .= $block ^ $key;
                    }
                }
                if ($this->continuousBuffer) {
                    $this->decryptIV = $xor;
                    if ($start = strlen($ciphertext) % $block_size) {
                        $buffer['ciphertext'] = substr($key, $start) . $buffer['ciphertext'];
                    }
                }
                break;
            case self::MODE_CFB:
                if ($this->continuousBuffer) {
                    $iv =& $this->decryptIV;
                    $pos =& $buffer['pos'];
                } else {
                    $iv = $this->decryptIV;
                    $pos = 0;
                }
                $len = strlen($ciphertext);
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = $block_size - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len -= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos += $len;
                        $len = 0;
                    }
                    $plaintext = substr($iv, $orig_pos) ^ $ciphertext;
                    $iv = substr_replace($iv, substr($ciphertext, 0, $i), $orig_pos, $i);
                }
                while ($len >= $block_size) {
                    $iv = $this->encryptBlock($iv);
                    $cb = substr($ciphertext, $i, $block_size);
                    $plaintext .= $iv ^ $cb;
                    $iv = $cb;
                    $len -= $block_size;
                    $i += $block_size;
                }
                if ($len) {
                    $iv = $this->encryptBlock($iv);
                    $plaintext .= $iv ^ substr($ciphertext, $i);
                    $iv = substr_replace($iv, substr($ciphertext, $i), 0, $len);
                    $pos = $len;
                }
                break;
            case self::MODE_CFB8:
                $plaintext = '';
                $len = strlen($ciphertext);
                $iv = $this->decryptIV;
                for ($i = 0; $i < $len; ++$i) {
                    $plaintext .= $ciphertext[$i] ^ $this->encryptBlock($iv);
                    $iv = substr($iv, 1) . $ciphertext[$i];
                }
                if ($this->continuousBuffer) {
                    if ($len >= $block_size) {
                        $this->decryptIV = substr($ciphertext, -$block_size);
                    } else {
                        $this->decryptIV = substr($this->decryptIV, $len - $block_size) . substr($ciphertext, -$len);
                    }
                }
                break;
            case self::MODE_OFB8:
                $plaintext = '';
                $len = strlen($ciphertext);
                $iv = $this->decryptIV;
                for ($i = 0; $i < $len; ++$i) {
                    $xor = $this->encryptBlock($iv);
                    $plaintext .= $ciphertext[$i] ^ $xor;
                    $iv = substr($iv, 1) . $xor[0];
                }
                if ($this->continuousBuffer) {
                    $this->decryptIV = $iv;
                }
                break;
            case self::MODE_OFB:
                $xor = $this->decryptIV;
                if (strlen($buffer['xor'])) {
                    for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                        $block = substr($ciphertext, $i, $block_size);
                        if (strlen($block) > strlen($buffer['xor'])) {
                            $xor = $this->encryptBlock($xor);
                            $buffer['xor'] .= $xor;
                        }
                        $key = Strings::shift($buffer['xor'], $block_size);
                        $plaintext .= $block ^ $key;
                    }
                } else {
                    for ($i = 0; $i < strlen($ciphertext); $i += $block_size) {
                        $xor = $this->encryptBlock($xor);
                        $plaintext .= substr($ciphertext, $i, $block_size) ^ $xor;
                    }
                    $key = $xor;
                }
                if ($this->continuousBuffer) {
                    $this->decryptIV = $xor;
                    if ($start = strlen($ciphertext) % $block_size) {
                        $buffer['xor'] = substr($key, $start) . $buffer['xor'];
                    }
                }
                break;
            case self::MODE_STREAM:
                $plaintext = $this->decryptBlock($ciphertext);
                break;
        }
        return $this->paddable ? $this->unpad($plaintext) : $plaintext;
    }
    public function getTag($length = 16)
    {
        if ($this->mode != self::MODE_GCM && !$this->usePoly1305) {
            throw new BadMethodCallException('Authentication tags are only utilized in GCM mode or with Poly1305');
        }
        if ($this->newtag === \false) {
            throw new BadMethodCallException('A tag can only be returned after a round of encryption has been performed');
        }
        if ($length < 4 || $length > 16) {
            throw new LengthException('The authentication tag must be between 4 and 16 bytes long');
        }
        return ($length == 16) ? $this->newtag : substr($this->newtag, 0, $length);
    }
    public function setTag($tag)
    {
        if ($this->usePoly1305 && !isset($this->poly1305Key) && method_exists($this, 'createPoly1305Key')) {
            $this->createPoly1305Key();
        }
        if ($this->mode != self::MODE_GCM && !$this->usePoly1305) {
            throw new BadMethodCallException('Authentication tags are only utilized in GCM mode or with Poly1305');
        }
        $length = strlen($tag);
        if ($length < 4 || $length > 16) {
            throw new LengthException('The authentication tag must be between 4 and 16 bytes long');
        }
        $this->oldtag = $tag;
    }
    protected function getIV($iv)
    {
        return ($this->mode == self::MODE_ECB) ? str_repeat("\x00", $this->block_size) : $iv;
    }
    private function openssl_ctr_process($plaintext, &$encryptIV, &$buffer)
    {
        $ciphertext = '';
        $block_size = $this->block_size;
        $key = $this->key;
        if ($this->openssl_emulate_ctr) {
            $xor = $encryptIV;
            if (strlen($buffer['ciphertext'])) {
                for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                    $block = substr($plaintext, $i, $block_size);
                    if (strlen($block) > strlen($buffer['ciphertext'])) {
                        $buffer['ciphertext'] .= openssl_encrypt($xor, $this->cipher_name_openssl_ecb, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                    }
                    Strings::increment_str($xor);
                    $otp = Strings::shift($buffer['ciphertext'], $block_size);
                    $ciphertext .= $block ^ $otp;
                }
            } else {
                for ($i = 0; $i < strlen($plaintext); $i += $block_size) {
                    $block = substr($plaintext, $i, $block_size);
                    $otp = openssl_encrypt($xor, $this->cipher_name_openssl_ecb, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
                    Strings::increment_str($xor);
                    $ciphertext .= $block ^ $otp;
                }
            }
            if ($this->continuousBuffer) {
                $encryptIV = $xor;
                if ($start = strlen($plaintext) % $block_size) {
                    $buffer['ciphertext'] = substr($key, $start) . $buffer['ciphertext'];
                }
            }
            return $ciphertext;
        }
        if (strlen($buffer['ciphertext'])) {
            $ciphertext = $plaintext ^ Strings::shift($buffer['ciphertext'], strlen($plaintext));
            $plaintext = substr($plaintext, strlen($ciphertext));
            if (!strlen($plaintext)) {
                return $ciphertext;
            }
        }
        $overflow = strlen($plaintext) % $block_size;
        if ($overflow) {
            $plaintext2 = Strings::pop($plaintext, $overflow);
            $encrypted = openssl_encrypt($plaintext . str_repeat("\x00", $block_size), $this->cipher_name_openssl, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $encryptIV);
            $temp = Strings::pop($encrypted, $block_size);
            $ciphertext .= $encrypted . ($plaintext2 ^ $temp);
            if ($this->continuousBuffer) {
                $buffer['ciphertext'] = substr($temp, $overflow);
                $encryptIV = $temp;
            }
        } elseif (!strlen($buffer['ciphertext'])) {
            $ciphertext .= openssl_encrypt($plaintext . str_repeat("\x00", $block_size), $this->cipher_name_openssl, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $encryptIV);
            $temp = Strings::pop($ciphertext, $block_size);
            if ($this->continuousBuffer) {
                $encryptIV = $temp;
            }
        }
        if ($this->continuousBuffer) {
            $encryptIV = openssl_decrypt($encryptIV, $this->cipher_name_openssl_ecb, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING);
            if ($overflow) {
                Strings::increment_str($encryptIV);
            }
        }
        return $ciphertext;
    }
    private function openssl_ofb_process($plaintext, &$encryptIV, &$buffer)
    {
        if (strlen($buffer['xor'])) {
            $ciphertext = $plaintext ^ $buffer['xor'];
            $buffer['xor'] = substr($buffer['xor'], strlen($ciphertext));
            $plaintext = substr($plaintext, strlen($ciphertext));
        } else {
            $ciphertext = '';
        }
        $block_size = $this->block_size;
        $len = strlen($plaintext);
        $key = $this->key;
        $overflow = $len % $block_size;
        if (strlen($plaintext)) {
            if ($overflow) {
                $ciphertext .= openssl_encrypt(substr($plaintext, 0, -$overflow) . str_repeat("\x00", $block_size), $this->cipher_name_openssl, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $encryptIV);
                $xor = Strings::pop($ciphertext, $block_size);
                if ($this->continuousBuffer) {
                    $encryptIV = $xor;
                }
                $ciphertext .= Strings::shift($xor, $overflow) ^ substr($plaintext, -$overflow);
                if ($this->continuousBuffer) {
                    $buffer['xor'] = $xor;
                }
            } else {
                $ciphertext = openssl_encrypt($plaintext, $this->cipher_name_openssl, $key, \OPENSSL_RAW_DATA | \OPENSSL_ZERO_PADDING, $encryptIV);
                if ($this->continuousBuffer) {
                    $encryptIV = substr($ciphertext, -$block_size) ^ substr($plaintext, -$block_size);
                }
            }
        }
        return $ciphertext;
    }
    protected function openssl_translate_mode()
    {
        switch ($this->mode) {
            case self::MODE_ECB:
                return 'ecb';
            case self::MODE_CBC:
                return 'cbc';
            case self::MODE_CTR:
            case self::MODE_GCM:
                return 'ctr';
            case self::MODE_CFB:
                return 'cfb';
            case self::MODE_CFB8:
                return 'cfb8';
            case self::MODE_OFB:
                return 'ofb';
        }
    }
    public function enablePadding()
    {
        $this->padding = \true;
    }
    public function disablePadding()
    {
        $this->padding = \false;
    }
    public function enableContinuousBuffer()
    {
        if ($this->mode == self::MODE_ECB) {
            return;
        }
        if ($this->mode == self::MODE_GCM) {
            throw new BadMethodCallException('This mode does not run in continuous mode');
        }
        $this->continuousBuffer = \true;
        $this->setEngine();
    }
    public function disableContinuousBuffer()
    {
        if ($this->mode == self::MODE_ECB) {
            return;
        }
        if (!$this->continuousBuffer) {
            return;
        }
        $this->continuousBuffer = \false;
        $this->setEngine();
    }
    protected function isValidEngineHelper($engine)
    {
        switch ($engine) {
            case self::ENGINE_OPENSSL:
                $this->openssl_emulate_ctr = \false;
                $result = $this->cipher_name_openssl && extension_loaded('openssl');
                if (!$result) {
                    return \false;
                }
                $methods = openssl_get_cipher_methods();
                if (in_array($this->cipher_name_openssl, $methods)) {
                    return \true;
                }
                switch ($this->mode) {
                    case self::MODE_CTR:
                        if (in_array($this->cipher_name_openssl_ecb, $methods)) {
                            $this->openssl_emulate_ctr = \true;
                            return \true;
                        }
                }
                return \false;
            case self::ENGINE_MCRYPT:
                set_error_handler(function () {
                });
                $result = $this->cipher_name_mcrypt && extension_loaded('mcrypt') && in_array($this->cipher_name_mcrypt, mcrypt_list_algorithms());
                restore_error_handler();
                return $result;
            case self::ENGINE_EVAL:
                return method_exists($this, 'setupInlineCrypt');
            case self::ENGINE_INTERNAL:
                return \true;
        }
        return \false;
    }
    public function isValidEngine($engine)
    {
        static $reverseMap;
        if (!isset($reverseMap)) {
            $reverseMap = array_map('strtolower', self::ENGINE_MAP);
            $reverseMap = array_flip($reverseMap);
        }
        $engine = strtolower($engine);
        if (!isset($reverseMap[$engine])) {
            return \false;
        }
        return $this->isValidEngineHelper($reverseMap[$engine]);
    }
    public function setPreferredEngine($engine)
    {
        static $reverseMap;
        if (!isset($reverseMap)) {
            $reverseMap = array_map('strtolower', self::ENGINE_MAP);
            $reverseMap = array_flip($reverseMap);
        }
        $engine = is_string($engine) ? strtolower($engine) : '';
        $this->preferredEngine = isset($reverseMap[$engine]) ? $reverseMap[$engine] : self::ENGINE_LIBSODIUM;
        $this->setEngine();
    }
    public function getEngine()
    {
        return self::ENGINE_MAP[$this->engine];
    }
    protected function setEngine()
    {
        $this->engine = null;
        $candidateEngines = [self::ENGINE_LIBSODIUM, self::ENGINE_OPENSSL_GCM, self::ENGINE_OPENSSL, self::ENGINE_MCRYPT, self::ENGINE_EVAL];
        if (isset($this->preferredEngine)) {
            $temp = [$this->preferredEngine];
            $candidateEngines = array_merge($temp, array_diff($candidateEngines, $temp));
        }
        foreach ($candidateEngines as $engine) {
            if ($this->isValidEngineHelper($engine)) {
                $this->engine = $engine;
                break;
            }
        }
        if (!$this->engine) {
            $this->engine = self::ENGINE_INTERNAL;
        }
        if ($this->engine != self::ENGINE_MCRYPT && $this->enmcrypt) {
            set_error_handler(function () {
            });
            mcrypt_module_close($this->enmcrypt);
            mcrypt_module_close($this->demcrypt);
            $this->enmcrypt = null;
            $this->demcrypt = null;
            if ($this->ecb) {
                mcrypt_module_close($this->ecb);
                $this->ecb = null;
            }
            restore_error_handler();
        }
        $this->changed = $this->nonIVChanged = \true;
    }
    abstract protected function encryptBlock($in);
    abstract protected function decryptBlock($in);
    abstract protected function setupKey();
    protected function setup()
    {
        if (!$this->changed) {
            return;
        }
        $this->changed = \false;
        if ($this->usePoly1305 && !isset($this->poly1305Key) && method_exists($this, 'createPoly1305Key')) {
            $this->createPoly1305Key();
        }
        $this->enbuffer = $this->debuffer = ['ciphertext' => '', 'xor' => '', 'pos' => 0, 'enmcrypt_init' => \true];
        if ($this->usesNonce()) {
            if ($this->nonce === \false) {
                throw new InsufficientSetupException('No nonce has been defined');
            }
            if ($this->mode == self::MODE_GCM && !in_array($this->engine, [self::ENGINE_LIBSODIUM, self::ENGINE_OPENSSL_GCM])) {
                $this->setupGCM();
            }
        } else {
            $this->iv = $this->origIV;
        }
        if ($this->iv === \false && !in_array($this->mode, [self::MODE_STREAM, self::MODE_ECB])) {
            if ($this->mode != self::MODE_GCM || !in_array($this->engine, [self::ENGINE_LIBSODIUM, self::ENGINE_OPENSSL_GCM])) {
                throw new InsufficientSetupException('No IV has been defined');
            }
        }
        if ($this->key === \false) {
            throw new InsufficientSetupException('No key has been defined');
        }
        $this->encryptIV = $this->decryptIV = $this->iv;
        switch ($this->engine) {
            case self::ENGINE_MCRYPT:
                $this->enchanged = $this->dechanged = \true;
                set_error_handler(function () {
                });
                if (!isset($this->enmcrypt)) {
                    static $mcrypt_modes = [self::MODE_CTR => 'ctr', self::MODE_ECB => \MCRYPT_MODE_ECB, self::MODE_CBC => \MCRYPT_MODE_CBC, self::MODE_CFB => 'ncfb', self::MODE_CFB8 => \MCRYPT_MODE_CFB, self::MODE_OFB => \MCRYPT_MODE_NOFB, self::MODE_OFB8 => \MCRYPT_MODE_OFB, self::MODE_STREAM => \MCRYPT_MODE_STREAM];
                    $this->demcrypt = mcrypt_module_open($this->cipher_name_mcrypt, '', $mcrypt_modes[$this->mode], '');
                    $this->enmcrypt = mcrypt_module_open($this->cipher_name_mcrypt, '', $mcrypt_modes[$this->mode], '');
                    if ($this->mode == self::MODE_CFB) {
                        $this->ecb = mcrypt_module_open($this->cipher_name_mcrypt, '', \MCRYPT_MODE_ECB, '');
                    }
                }
                if ($this->mode == self::MODE_CFB) {
                    mcrypt_generic_init($this->ecb, $this->key, str_repeat("\x00", $this->block_size));
                }
                restore_error_handler();
                break;
            case self::ENGINE_INTERNAL:
                $this->setupKey();
                break;
            case self::ENGINE_EVAL:
                if ($this->nonIVChanged) {
                    $this->setupKey();
                    $this->setupInlineCrypt();
                }
        }
        $this->nonIVChanged = \false;
    }
    protected function pad($text)
    {
        $length = strlen($text);
        if (!$this->padding) {
            if ($length % $this->block_size == 0) {
                return $text;
            } else {
                throw new LengthException("The plaintext's length ({$length}) is not a multiple of the block size ({$this->block_size}). Try enabling padding.");
            }
        }
        $pad = $this->block_size - $length % $this->block_size;
        return str_pad($text, $length + $pad, chr($pad));
    }
    protected function unpad($text)
    {
        if (!$this->padding) {
            return $text;
        }
        $length = ord($text[strlen($text) - 1]);
        if (!$length || $length > $this->block_size) {
            throw new BadDecryptionException("The ciphertext has an invalid padding length ({$length}) compared to the block size ({$this->block_size})");
        }
        return substr($text, 0, -$length);
    }
    protected function createInlineCryptFunction($cipher_code)
    {
        $block_size = $this->block_size;
        $init_crypt = isset($cipher_code['init_crypt']) ? $cipher_code['init_crypt'] : '';
        $init_encrypt = isset($cipher_code['init_encrypt']) ? $cipher_code['init_encrypt'] : '';
        $init_decrypt = isset($cipher_code['init_decrypt']) ? $cipher_code['init_decrypt'] : '';
        $encrypt_block = $cipher_code['encrypt_block'];
        $decrypt_block = $cipher_code['decrypt_block'];
        switch ($this->mode) {
            case self::MODE_ECB:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_plaintext_len = strlen($_text);

                    for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                        $in = substr($_text, $_i, ' . $block_size . ');
                        ' . $encrypt_block . '
                        $_ciphertext.= $in;
                    }

                    return $_ciphertext;
                    ';
                $decrypt = $init_decrypt . '
                    $_plaintext = "";
                    $_text = str_pad($_text, strlen($_text) + (' . $block_size . ' - strlen($_text) % ' . $block_size . ') % ' . $block_size . ', chr(0));
                    $_ciphertext_len = strlen($_text);

                    for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                        $in = substr($_text, $_i, ' . $block_size . ');
                        ' . $decrypt_block . '
                        $_plaintext.= $in;
                    }

                    return $this->unpad($_plaintext);
                    ';
                break;
            case self::MODE_CTR:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_plaintext_len = strlen($_text);
                    $_xor = $this->encryptIV;
                    $_buffer = &$this->enbuffer;
                    if (strlen($_buffer["ciphertext"])) {
                        for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            if (strlen($_block) > strlen($_buffer["ciphertext"])) {
                                $in = $_xor;
                                ' . $encrypt_block . '
                                \phpseclib3\Common\Functions\Strings::increment_str($_xor);
                                $_buffer["ciphertext"].= $in;
                            }
                            $_key = \phpseclib3\Common\Functions\Strings::shift($_buffer["ciphertext"], ' . $block_size . ');
                            $_ciphertext.= $_block ^ $_key;
                        }
                    } else {
                        for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            $in = $_xor;
                            ' . $encrypt_block . '
                            \phpseclib3\Common\Functions\Strings::increment_str($_xor);
                            $_key = $in;
                            $_ciphertext.= $_block ^ $_key;
                        }
                    }
                    if ($this->continuousBuffer) {
                        $this->encryptIV = $_xor;
                        if ($_start = $_plaintext_len % ' . $block_size . ') {
                            $_buffer["ciphertext"] = substr($_key, $_start) . $_buffer["ciphertext"];
                        }
                    }

                    return $_ciphertext;
                ';
                $decrypt = $init_encrypt . '
                    $_plaintext = "";
                    $_ciphertext_len = strlen($_text);
                    $_xor = $this->decryptIV;
                    $_buffer = &$this->debuffer;

                    if (strlen($_buffer["ciphertext"])) {
                        for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            if (strlen($_block) > strlen($_buffer["ciphertext"])) {
                                $in = $_xor;
                                ' . $encrypt_block . '
                                \phpseclib3\Common\Functions\Strings::increment_str($_xor);
                                $_buffer["ciphertext"].= $in;
                            }
                            $_key = \phpseclib3\Common\Functions\Strings::shift($_buffer["ciphertext"], ' . $block_size . ');
                            $_plaintext.= $_block ^ $_key;
                        }
                    } else {
                        for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            $in = $_xor;
                            ' . $encrypt_block . '
                            \phpseclib3\Common\Functions\Strings::increment_str($_xor);
                            $_key = $in;
                            $_plaintext.= $_block ^ $_key;
                        }
                    }
                    if ($this->continuousBuffer) {
                        $this->decryptIV = $_xor;
                        if ($_start = $_ciphertext_len % ' . $block_size . ') {
                            $_buffer["ciphertext"] = substr($_key, $_start) . $_buffer["ciphertext"];
                        }
                    }

                    return $_plaintext;
                    ';
                break;
            case self::MODE_CFB:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_buffer = &$this->enbuffer;

                    if ($this->continuousBuffer) {
                        $_iv = &$this->encryptIV;
                        $_pos = &$_buffer["pos"];
                    } else {
                        $_iv = $this->encryptIV;
                        $_pos = 0;
                    }
                    $_len = strlen($_text);
                    $_i = 0;
                    if ($_pos) {
                        $_orig_pos = $_pos;
                        $_max = ' . $block_size . ' - $_pos;
                        if ($_len >= $_max) {
                            $_i = $_max;
                            $_len-= $_max;
                            $_pos = 0;
                        } else {
                            $_i = $_len;
                            $_pos+= $_len;
                            $_len = 0;
                        }
                        $_ciphertext = substr($_iv, $_orig_pos) ^ $_text;
                        $_iv = substr_replace($_iv, $_ciphertext, $_orig_pos, $_i);
                    }
                    while ($_len >= ' . $block_size . ') {
                        $in = $_iv;
                        ' . $encrypt_block . ';
                        $_iv = $in ^ substr($_text, $_i, ' . $block_size . ');
                        $_ciphertext.= $_iv;
                        $_len-= ' . $block_size . ';
                        $_i+= ' . $block_size . ';
                    }
                    if ($_len) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_iv = $in;
                        $_block = $_iv ^ substr($_text, $_i);
                        $_iv = substr_replace($_iv, $_block, 0, $_len);
                        $_ciphertext.= $_block;
                        $_pos = $_len;
                    }
                    return $_ciphertext;
                ';
                $decrypt = $init_encrypt . '
                    $_plaintext = "";
                    $_buffer = &$this->debuffer;

                    if ($this->continuousBuffer) {
                        $_iv = &$this->decryptIV;
                        $_pos = &$_buffer["pos"];
                    } else {
                        $_iv = $this->decryptIV;
                        $_pos = 0;
                    }
                    $_len = strlen($_text);
                    $_i = 0;
                    if ($_pos) {
                        $_orig_pos = $_pos;
                        $_max = ' . $block_size . ' - $_pos;
                        if ($_len >= $_max) {
                            $_i = $_max;
                            $_len-= $_max;
                            $_pos = 0;
                        } else {
                            $_i = $_len;
                            $_pos+= $_len;
                            $_len = 0;
                        }
                        $_plaintext = substr($_iv, $_orig_pos) ^ $_text;
                        $_iv = substr_replace($_iv, substr($_text, 0, $_i), $_orig_pos, $_i);
                    }
                    while ($_len >= ' . $block_size . ') {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_iv = $in;
                        $cb = substr($_text, $_i, ' . $block_size . ');
                        $_plaintext.= $_iv ^ $cb;
                        $_iv = $cb;
                        $_len-= ' . $block_size . ';
                        $_i+= ' . $block_size . ';
                    }
                    if ($_len) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_iv = $in;
                        $_plaintext.= $_iv ^ substr($_text, $_i);
                        $_iv = substr_replace($_iv, substr($_text, $_i), 0, $_len);
                        $_pos = $_len;
                    }

                    return $_plaintext;
                    ';
                break;
            case self::MODE_CFB8:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_len = strlen($_text);
                    $_iv = $this->encryptIV;

                    for ($_i = 0; $_i < $_len; ++$_i) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_ciphertext .= ($_c = $_text[$_i] ^ $in);
                        $_iv = substr($_iv, 1) . $_c;
                    }

                    if ($this->continuousBuffer) {
                        if ($_len >= ' . $block_size . ') {
                            $this->encryptIV = substr($_ciphertext, -' . $block_size . ');
                        } else {
                            $this->encryptIV = substr($this->encryptIV, $_len - ' . $block_size . ') . substr($_ciphertext, -$_len);
                        }
                    }

                    return $_ciphertext;
                    ';
                $decrypt = $init_encrypt . '
                    $_plaintext = "";
                    $_len = strlen($_text);
                    $_iv = $this->decryptIV;

                    for ($_i = 0; $_i < $_len; ++$_i) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_plaintext .= $_text[$_i] ^ $in;
                        $_iv = substr($_iv, 1) . $_text[$_i];
                    }

                    if ($this->continuousBuffer) {
                        if ($_len >= ' . $block_size . ') {
                            $this->decryptIV = substr($_text, -' . $block_size . ');
                        } else {
                            $this->decryptIV = substr($this->decryptIV, $_len - ' . $block_size . ') . substr($_text, -$_len);
                        }
                    }

                    return $_plaintext;
                    ';
                break;
            case self::MODE_OFB8:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_len = strlen($_text);
                    $_iv = $this->encryptIV;

                    for ($_i = 0; $_i < $_len; ++$_i) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_ciphertext.= $_text[$_i] ^ $in;
                        $_iv = substr($_iv, 1) . $in[0];
                    }

                    if ($this->continuousBuffer) {
                        $this->encryptIV = $_iv;
                    }

                    return $_ciphertext;
                    ';
                $decrypt = $init_encrypt . '
                    $_plaintext = "";
                    $_len = strlen($_text);
                    $_iv = $this->decryptIV;

                    for ($_i = 0; $_i < $_len; ++$_i) {
                        $in = $_iv;
                        ' . $encrypt_block . '
                        $_plaintext.= $_text[$_i] ^ $in;
                        $_iv = substr($_iv, 1) . $in[0];
                    }

                    if ($this->continuousBuffer) {
                        $this->decryptIV = $_iv;
                    }

                    return $_plaintext;
                    ';
                break;
            case self::MODE_OFB:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_plaintext_len = strlen($_text);
                    $_xor = $this->encryptIV;
                    $_buffer = &$this->enbuffer;

                    if (strlen($_buffer["xor"])) {
                        for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            if (strlen($_block) > strlen($_buffer["xor"])) {
                                $in = $_xor;
                                ' . $encrypt_block . '
                                $_xor = $in;
                                $_buffer["xor"].= $_xor;
                            }
                            $_key = \phpseclib3\Common\Functions\Strings::shift($_buffer["xor"], ' . $block_size . ');
                            $_ciphertext.= $_block ^ $_key;
                        }
                    } else {
                        for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                            $in = $_xor;
                            ' . $encrypt_block . '
                            $_xor = $in;
                            $_ciphertext.= substr($_text, $_i, ' . $block_size . ') ^ $_xor;
                        }
                        $_key = $_xor;
                    }
                    if ($this->continuousBuffer) {
                        $this->encryptIV = $_xor;
                        if ($_start = $_plaintext_len % ' . $block_size . ') {
                             $_buffer["xor"] = substr($_key, $_start) . $_buffer["xor"];
                        }
                    }
                    return $_ciphertext;
                    ';
                $decrypt = $init_encrypt . '
                    $_plaintext = "";
                    $_ciphertext_len = strlen($_text);
                    $_xor = $this->decryptIV;
                    $_buffer = &$this->debuffer;

                    if (strlen($_buffer["xor"])) {
                        for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                            $_block = substr($_text, $_i, ' . $block_size . ');
                            if (strlen($_block) > strlen($_buffer["xor"])) {
                                $in = $_xor;
                                ' . $encrypt_block . '
                                $_xor = $in;
                                $_buffer["xor"].= $_xor;
                            }
                            $_key = \phpseclib3\Common\Functions\Strings::shift($_buffer["xor"], ' . $block_size . ');
                            $_plaintext.= $_block ^ $_key;
                        }
                    } else {
                        for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                            $in = $_xor;
                            ' . $encrypt_block . '
                            $_xor = $in;
                            $_plaintext.= substr($_text, $_i, ' . $block_size . ') ^ $_xor;
                        }
                        $_key = $_xor;
                    }
                    if ($this->continuousBuffer) {
                        $this->decryptIV = $_xor;
                        if ($_start = $_ciphertext_len % ' . $block_size . ') {
                             $_buffer["xor"] = substr($_key, $_start) . $_buffer["xor"];
                        }
                    }
                    return $_plaintext;
                    ';
                break;
            case self::MODE_STREAM:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    ' . $encrypt_block . '
                    return $_ciphertext;
                    ';
                $decrypt = $init_decrypt . '
                    $_plaintext = "";
                    ' . $decrypt_block . '
                    return $_plaintext;
                    ';
                break;
            default:
                $encrypt = $init_encrypt . '
                    $_ciphertext = "";
                    $_plaintext_len = strlen($_text);

                    $in = $this->encryptIV;

                    for ($_i = 0; $_i < $_plaintext_len; $_i+= ' . $block_size . ') {
                        $in = substr($_text, $_i, ' . $block_size . ') ^ $in;
                        ' . $encrypt_block . '
                        $_ciphertext.= $in;
                    }

                    if ($this->continuousBuffer) {
                        $this->encryptIV = $in;
                    }

                    return $_ciphertext;
                    ';
                $decrypt = $init_decrypt . '
                    $_plaintext = "";
                    $_text = str_pad($_text, strlen($_text) + (' . $block_size . ' - strlen($_text) % ' . $block_size . ') % ' . $block_size . ', chr(0));
                    $_ciphertext_len = strlen($_text);

                    $_iv = $this->decryptIV;

                    for ($_i = 0; $_i < $_ciphertext_len; $_i+= ' . $block_size . ') {
                        $in = $_block = substr($_text, $_i, ' . $block_size . ');
                        ' . $decrypt_block . '
                        $_plaintext.= $in ^ $_iv;
                        $_iv = $_block;
                    }

                    if ($this->continuousBuffer) {
                        $this->decryptIV = $_iv;
                    }

                    return $this->unpad($_plaintext);
                    ';
                break;
        }
        eval('$func = function ($_action, $_text) { ' . $init_crypt . 'if ($_action == "encrypt") { ' . $encrypt . ' } else { ' . $decrypt . ' }};');
        return Closure::bind($func, $this, static::class);
    }
    protected static function safe_intval($x)
    {
        if (is_int($x)) {
            return $x;
        }
        if (self::$use_reg_intval) {
            return (\PHP_INT_SIZE == 4 && \PHP_VERSION_ID >= 80100) ? intval($x) : $x;
        }
        return fmod($x, 0x80000000) & 0x7fffffff | (fmod(floor($x / 0x80000000), 2) & 1) << 31;
    }
    protected static function safe_intval_inline()
    {
        if (self::$use_reg_intval) {
            return (\PHP_INT_SIZE == 4 && \PHP_VERSION_ID >= 80100) ? 'intval(%s)' : '%s';
        }
        $safeint = '(is_int($temp = %s) ? $temp : (fmod($temp, 0x80000000) & 0x7FFFFFFF) | ';
        return $safeint . '((fmod(floor($temp / 0x80000000), 2) & 1) << 31))';
    }
    private function setupGCM()
    {
        if (!$this->h || $this->hKey != $this->key) {
            $cipher = new static('ecb');
            $cipher->setKey($this->key);
            $cipher->disablePadding();
            $this->h = self::$gcmField->newInteger(Strings::switchEndianness($cipher->encrypt("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00")));
            $this->hKey = $this->key;
        }
        if (strlen($this->nonce) == 12) {
            $this->iv = $this->nonce . "\x00\x00\x00\x01";
        } else {
            $this->iv = $this->ghash(self::nullPad128($this->nonce) . str_repeat("\x00", 8) . self::len64($this->nonce));
        }
    }
    private function ghash($x)
    {
        $h = $this->h;
        $y = ["\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"];
        $x = str_split($x, 16);
        $n = 0;
        foreach ($x as $xn) {
            $xn = Strings::switchEndianness($xn);
            $t = $y[$n] ^ $xn;
            $temp = self::$gcmField->newInteger($t);
            $y[++$n] = $temp->multiply($h)->toBytes();
            $y[$n] = substr($y[$n], 1);
        }
        $y[$n] = Strings::switchEndianness($y[$n]);
        return $y[$n];
    }
    private static function len64($str)
    {
        return "\x00\x00\x00\x00" . pack('N', 8 * strlen($str));
    }
    protected static function nullPad128($str)
    {
        $len = strlen($str);
        return $str . str_repeat("\x00", 16 * ceil($len / 16) - $len);
    }
    protected function poly1305($text)
    {
        $s = $this->poly1305Key;
        $r = Strings::shift($s, 16);
        $r = strrev($r);
        $r &= "\x0f\xff\xff\xfc\x0f\xff\xff\xfc\x0f\xff\xff\xfc\x0f\xff\xff\xff";
        $s = strrev($s);
        $r = self::$poly1305Field->newInteger(new BigInteger($r, 256));
        $s = self::$poly1305Field->newInteger(new BigInteger($s, 256));
        $a = self::$poly1305Field->newInteger(new BigInteger());
        $blocks = str_split($text, 16);
        foreach ($blocks as $block) {
            $n = strrev($block . chr(1));
            $n = self::$poly1305Field->newInteger(new BigInteger($n, 256));
            $a = $a->add($n);
            $a = $a->multiply($r);
        }
        $r = $a->toBigInteger()->add($s->toBigInteger());
        $mask = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";
        return strrev($r->toBytes()) & $mask;
    }
    public function getMode()
    {
        return array_flip(self::MODE_MAP)[$this->mode];
    }
    public function continuousBufferEnabled()
    {
        return $this->continuousBuffer;
    }
}
