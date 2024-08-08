<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface ResponseInterface extends MessageInterface
{
    public function getStatusCode();
    /**
     * @param int $code
     * @param string $reasonPhrase
     */
    public function withStatus($code, $reasonPhrase = '');
    public function getReasonPhrase();
}
