<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
final class RedirectionException extends RuntimeException implements HttpException, RedirectionExceptionInterface
{
    use HttpExceptionTrait;
}
