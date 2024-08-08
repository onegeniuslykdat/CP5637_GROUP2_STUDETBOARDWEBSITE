<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
final class InvalidArgumentException extends \InvalidArgumentException implements TransportExceptionInterface
{
}
