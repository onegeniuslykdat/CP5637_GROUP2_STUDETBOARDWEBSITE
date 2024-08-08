<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common;

interface PublicKey
{
    public function verify($message, $signature);
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = []);
    public function getFingerprint($algorithm);
}
