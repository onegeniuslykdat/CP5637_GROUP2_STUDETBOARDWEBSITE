<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
final class TimeoutException extends TransportException implements TimeoutExceptionInterface
{
}
