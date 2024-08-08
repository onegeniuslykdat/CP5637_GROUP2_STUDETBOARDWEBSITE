<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use Stringable;
interface ResourceInterface extends Stringable
{
    public function __toString(): string;
}
