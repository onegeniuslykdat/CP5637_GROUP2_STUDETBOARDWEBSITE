<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
final class ServerException extends RuntimeException implements ServerExceptionInterface
{
    use HttpExceptionTrait;
}
