<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface ResponseFactoryInterface
{
    /**
     * @param int $code
     * @param string $reasonPhrase
     */
    public function createResponse($code = 200, $reasonPhrase = ''): ResponseInterface;
}
