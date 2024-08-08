<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use RuntimeException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
class TransportException extends RuntimeException implements TransportExceptionInterface
{
}
