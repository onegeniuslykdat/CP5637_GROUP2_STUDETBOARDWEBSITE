<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use LengthException;
class TripleDES extends DES
{
    const MODE_3CBC = -2;
    const MODE_CBC3 = self::MODE_CBC;
    protected $key_length = 24;
    protected $cipher_name_mcrypt = 'tripledes';
    protected $cfb_init_len = 750;
    protected $key_length_max = 24;
    private $mode_3cbc;
    private $des;
    public function __construct($mode)
    {
        switch (strtolower($mode)) {
            case '3cbc':
                parent::__construct('cbc');
                $this->mode_3cbc = \true;
                $this->des = [new DES('cbc'), new DES('cbc'), new DES('cbc')];
                $this->des[0]->disablePadding();
                $this->des[1]->disablePadding();
                $this->des[2]->disablePadding();
                break;
            case 'cbc3':
                $mode = 'cbc';
            default:
                parent::__construct($mode);
                if ($this->mode == self::MODE_STREAM) {
                    throw new BadModeException('Block ciphers cannot be ran in stream mode');
                }
        }
    }
    protected function isValidEngineHelper($engine)
    {
        if ($engine == self::ENGINE_OPENSSL) {
            $this->cipher_name_openssl_ecb = 'des-ede3';
            $mode = $this->openssl_translate_mode();
            $this->cipher_name_openssl = ($mode == 'ecb') ? 'des-ede3' : ('des-ede3-' . $mode);
        }
        return parent::isValidEngineHelper($engine);
    }
    public function setIV($iv)
    {
        parent::setIV($iv);
        if ($this->mode_3cbc) {
            $this->des[0]->setIV($iv);
            $this->des[1]->setIV($iv);
            $this->des[2]->setIV($iv);
        }
    }
    public function setKeyLength($length)
    {
        switch ($length) {
            case 128:
            case 192:
                break;
            default:
                throw new LengthException('Key size of ' . $length . ' bits is not supported by this algorithm. Only keys of sizes 128 or 192 bits are supported');
        }
        parent::setKeyLength($length);
    }
    public function setKey($key)
    {
        if ($this->explicit_key_length !== \false && strlen($key) != $this->explicit_key_length) {
            throw new LengthException('Key length has already been set to ' . $this->explicit_key_length . ' bytes and this key is ' . strlen($key) . ' bytes');
        }
        switch (strlen($key)) {
            case 16:
                $key .= substr($key, 0, 8);
                break;
            case 24:
                break;
            default:
                throw new LengthException('Key of size ' . strlen($key) . ' not supported by this algorithm. Only keys of sizes 16 or 24 are supported');
        }
        $this->key = $key;
        $this->key_length = strlen($key);
        $this->changed = $this->nonIVChanged = \true;
        $this->setEngine();
        if ($this->mode_3cbc) {
            $this->des[0]->setKey(substr($key, 0, 8));
            $this->des[1]->setKey(substr($key, 8, 8));
            $this->des[2]->setKey(substr($key, 16, 8));
        }
    }
    public function encrypt($plaintext)
    {
        if ($this->mode_3cbc && strlen($this->key) > 8) {
            return $this->des[2]->encrypt($this->des[1]->decrypt($this->des[0]->encrypt($this->pad($plaintext))));
        }
        return parent::encrypt($plaintext);
    }
    public function decrypt($ciphertext)
    {
        if ($this->mode_3cbc && strlen($this->key) > 8) {
            return $this->unpad($this->des[0]->decrypt($this->des[1]->encrypt($this->des[2]->decrypt(str_pad($ciphertext, strlen($ciphertext) + 7 & 0xfffffff8, "\x00")))));
        }
        return parent::decrypt($ciphertext);
    }
    public function enableContinuousBuffer()
    {
        parent::enableContinuousBuffer();
        if ($this->mode_3cbc) {
            $this->des[0]->enableContinuousBuffer();
            $this->des[1]->enableContinuousBuffer();
            $this->des[2]->enableContinuousBuffer();
        }
    }
    public function disableContinuousBuffer()
    {
        parent::disableContinuousBuffer();
        if ($this->mode_3cbc) {
            $this->des[0]->disableContinuousBuffer();
            $this->des[1]->disableContinuousBuffer();
            $this->des[2]->disableContinuousBuffer();
        }
    }
    protected function setupKey()
    {
        switch (\true) {
            case strlen($this->key) <= 8:
                $this->des_rounds = 1;
                break;
            default:
                $this->des_rounds = 3;
                if ($this->mode_3cbc) {
                    $this->des[0]->setupKey();
                    $this->des[1]->setupKey();
                    $this->des[2]->setupKey();
                    return;
                }
        }
        parent::setupKey();
    }
    public function setPreferredEngine($engine)
    {
        if ($this->mode_3cbc) {
            $this->des[0]->setPreferredEngine($engine);
            $this->des[1]->setPreferredEngine($engine);
            $this->des[2]->setPreferredEngine($engine);
        }
        parent::setPreferredEngine($engine);
    }
}
