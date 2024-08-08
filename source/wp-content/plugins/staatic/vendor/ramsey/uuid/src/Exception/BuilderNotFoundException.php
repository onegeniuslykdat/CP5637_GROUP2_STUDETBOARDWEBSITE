<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Exception;

use RuntimeException as PhpRuntimeException;
class BuilderNotFoundException extends PhpRuntimeException implements UuidExceptionInterface
{
}
