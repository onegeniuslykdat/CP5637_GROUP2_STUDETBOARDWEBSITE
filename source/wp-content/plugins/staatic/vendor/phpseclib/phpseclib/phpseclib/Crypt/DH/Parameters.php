<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DH;

use Staatic\Vendor\phpseclib3\Crypt\DH;
final class Parameters extends DH
{
    /**
     * @param mixed[] $options
     */
    public function toString($type = 'PKCS1', $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');
        return $type::saveParameters($this->prime, $this->base, $options);
    }
}
