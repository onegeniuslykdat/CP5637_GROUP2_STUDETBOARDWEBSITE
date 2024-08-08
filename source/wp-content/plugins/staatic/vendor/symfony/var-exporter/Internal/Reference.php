<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

class Reference
{
    /**
     * @readonly
     * @var int
     */
    public $id;
    /**
     * @readonly
     * @var mixed
     */
    public $value = null;
    /**
     * @var int
     */
    public $count = 0;
    /**
     * @param mixed $value
     */
    public function __construct(int $id, $value = null)
    {
        $this->id = $id;
        $this->value = $value;
    }
}
