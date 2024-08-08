<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC;

use Staatic\Vendor\phpseclib3\Crypt\EC;
final class Parameters extends EC
{
    /**
     * @param mixed[] $options
     */
    public function toString($type = 'PKCS1', $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');
        return $type::saveParameters($this->curve, $options);
    }
}
