<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Exception;

use RuntimeException as PhpRuntimeException;
class TimeSourceException extends PhpRuntimeException implements UuidExceptionInterface
{
}
