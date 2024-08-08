<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
final class RedirectionException extends RuntimeException implements RedirectionExceptionInterface
{
    use HttpExceptionTrait;
}
