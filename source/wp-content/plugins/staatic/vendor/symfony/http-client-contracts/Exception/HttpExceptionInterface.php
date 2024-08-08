<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient\Exception;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
interface HttpExceptionInterface extends ExceptionInterface
{
    public function getResponse(): ResponseInterface;
}
