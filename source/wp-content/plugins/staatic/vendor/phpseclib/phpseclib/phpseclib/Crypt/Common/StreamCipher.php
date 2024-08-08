<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common;

abstract class StreamCipher extends SymmetricKey
{
    protected $block_size = 0;
    public function __construct()
    {
        parent::__construct('stream');
    }
    public function usesIV()
    {
        return \false;
    }
}
