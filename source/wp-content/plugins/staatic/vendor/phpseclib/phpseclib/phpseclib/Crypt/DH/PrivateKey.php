<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DH;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\PasswordProtected;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\DH;
final class PrivateKey extends DH
{
    use PasswordProtected;
    protected $privateKey;
    protected $publicKey;
    public function getPublicKey()
    {
        $type = self::validatePlugin('Keys', 'PKCS8', 'savePublicKey');
        if (!isset($this->publicKey)) {
            $this->publicKey = $this->base->powMod($this->privateKey, $this->prime);
        }
        $key = $type::savePublicKey($this->prime, $this->base, $this->publicKey);
        return DH::loadFormat('PKCS8', $key);
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePrivateKey');
        if (!isset($this->publicKey)) {
            $this->publicKey = $this->base->powMod($this->privateKey, $this->prime);
        }
        return $type::savePrivateKey($this->prime, $this->base, $this->privateKey, $this->publicKey, $this->password, $options);
    }
}
