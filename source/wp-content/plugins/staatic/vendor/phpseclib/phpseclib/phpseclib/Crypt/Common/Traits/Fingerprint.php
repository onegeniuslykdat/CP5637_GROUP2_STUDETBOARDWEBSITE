<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Traits;

use Staatic\Vendor\phpseclib3\Crypt\Hash;
trait Fingerprint
{
    public function getFingerprint($algorithm = 'md5')
    {
        $type = self::validatePlugin('Keys', 'OpenSSH', 'savePublicKey');
        if ($type === \false) {
            return \false;
        }
        $key = $this->toString('OpenSSH', ['binary' => \true]);
        if ($key === \false) {
            return \false;
        }
        switch ($algorithm) {
            case 'sha256':
                $hash = new Hash('sha256');
                $base = base64_encode($hash->hash($key));
                return substr($base, 0, strlen($base) - 1);
            case 'md5':
                return substr(chunk_split(md5($key), 2, ':'), 0, -1);
            default:
                return \false;
        }
    }
}
