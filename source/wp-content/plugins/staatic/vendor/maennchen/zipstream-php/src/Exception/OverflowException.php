<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Exception;

use Staatic\Vendor\ZipStream\Exception;
class OverflowException extends Exception
{
    public function __construct()
    {
        parent::__construct('File size exceeds limit of 32 bit integer. Please enable "zip64" option.');
    }
}
