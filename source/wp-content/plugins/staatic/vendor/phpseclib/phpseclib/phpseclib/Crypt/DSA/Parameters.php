<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA;

use Staatic\Vendor\phpseclib3\Crypt\DSA;
final class Parameters extends DSA
{
    /**
     * @param mixed[] $options
     */
    public function toString($type = 'PKCS1', $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');
        return $type::saveParameters($this->p, $this->q, $this->g, $options);
    }
}
