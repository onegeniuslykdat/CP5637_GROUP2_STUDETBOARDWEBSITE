<?php

namespace Staatic\Vendor\Symfony\Component\Config\Exception;

use Throwable;
use Exception;
class FileLoaderImportCircularReferenceException extends LoaderLoadException
{
    public function __construct(array $resources, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Circular reference detected in "%s" ("%s" > "%s").', $this->varToString($resources[0]), implode('" > "', $resources), $resources[0]);
        Exception::__construct($message, $code, $previous);
    }
}
