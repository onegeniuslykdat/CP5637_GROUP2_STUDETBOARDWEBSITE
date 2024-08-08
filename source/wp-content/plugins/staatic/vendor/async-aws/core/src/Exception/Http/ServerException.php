<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
class ServerException extends RuntimeException implements HttpException, ServerExceptionInterface
{
    use HttpExceptionTrait;
}
