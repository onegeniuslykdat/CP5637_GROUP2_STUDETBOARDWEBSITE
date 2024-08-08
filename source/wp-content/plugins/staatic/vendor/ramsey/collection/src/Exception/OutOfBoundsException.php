<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Exception;

use OutOfBoundsException as PhpOutOfBoundsException;
class OutOfBoundsException extends PhpOutOfBoundsException implements CollectionException
{
}
