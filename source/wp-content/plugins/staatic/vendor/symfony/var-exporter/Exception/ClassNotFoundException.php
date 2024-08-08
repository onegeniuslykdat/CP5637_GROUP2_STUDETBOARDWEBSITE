<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Exception;

use Exception;
use Throwable;
class ClassNotFoundException extends Exception implements ExceptionInterface
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Class "%s" not found.', $class), 0, $previous);
    }
}
