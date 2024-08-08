<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use RuntimeException;
use Staatic\Vendor\AsyncAws\Core\Exception\Exception;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
class NetworkException extends RuntimeException implements Exception, TransportExceptionInterface
{
}
