<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
interface StreamableInterface
{
    /**
     * @param bool $throw
     */
    public function toStream($throw = \true);
}
