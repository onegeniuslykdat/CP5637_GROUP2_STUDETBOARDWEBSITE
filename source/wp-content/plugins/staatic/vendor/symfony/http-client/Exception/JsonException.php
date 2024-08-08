<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Exception;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
final class JsonException extends \JsonException implements DecodingExceptionInterface
{
}
