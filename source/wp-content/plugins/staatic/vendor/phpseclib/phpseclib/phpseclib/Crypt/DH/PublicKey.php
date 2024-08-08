<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DH;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\Fingerprint;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\DH;
final class PublicKey extends DH
{
    use Fingerprint;
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePublicKey');
        return $type::savePublicKey($this->prime, $this->base, $this->publicKey, $options);
    }
    public function toBigInteger()
    {
        return $this->publicKey;
    }
}
