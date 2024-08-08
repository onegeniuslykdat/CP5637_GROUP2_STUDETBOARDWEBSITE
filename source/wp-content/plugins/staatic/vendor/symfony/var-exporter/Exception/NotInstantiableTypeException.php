<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Exception;

use Exception;
use Throwable;
class NotInstantiableTypeException extends Exception implements ExceptionInterface
{
    public function __construct(string $type, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Type "%s" is not instantiable.', $type), 0, $previous);
    }
}
