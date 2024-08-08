<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Traits;

trait PasswordProtected
{
    private $password = \false;
    public function withPassword($password = \false)
    {
        $new = clone $this;
        $new->password = $password;
        return $new;
    }
}
