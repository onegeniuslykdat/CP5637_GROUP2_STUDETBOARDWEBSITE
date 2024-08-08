<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Exception;

use RuntimeException as PhpRuntimeException;
class RandomSourceException extends PhpRuntimeException implements UuidExceptionInterface
{
}
