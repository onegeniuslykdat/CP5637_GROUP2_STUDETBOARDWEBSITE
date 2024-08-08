<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
class ClientException extends RuntimeException implements ClientExceptionInterface, HttpException
{
    use HttpExceptionTrait;
}
