<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;
class InvalidArgumentException extends PhpInvalidArgumentException implements CollectionException
{
}
