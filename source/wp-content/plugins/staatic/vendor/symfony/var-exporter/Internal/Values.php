<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

class Values
{
    public $values;
    public function __construct(array $values)
    {
        $this->values = $values;
    }
}
