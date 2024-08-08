<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common;

interface PrivateKey
{
    public function sign($message);
    public function getPublicKey();
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = []);
    public function withPassword($password = \false);
}
