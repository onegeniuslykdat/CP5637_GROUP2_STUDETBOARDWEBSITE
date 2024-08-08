<?php

namespace Staatic\Vendor\phpseclib3\System\SSH\Common\Traits;

use RuntimeException;
trait ReadBytes
{
    public function readBytes($length)
    {
        $temp = fread($this->fsock, $length);
        if (strlen($temp) != $length) {
            throw new RuntimeException("Expected {$length} bytes; got " . strlen($temp));
        }
        return $temp;
    }
}
