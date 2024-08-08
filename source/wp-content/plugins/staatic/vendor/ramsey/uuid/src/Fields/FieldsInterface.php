<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Fields;

use Serializable;
interface FieldsInterface extends Serializable
{
    public function getBytes(): string;
}
