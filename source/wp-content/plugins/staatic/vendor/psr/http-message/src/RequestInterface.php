<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface RequestInterface extends MessageInterface
{
    public function getRequestTarget();
    /**
     * @param string $requestTarget
     */
    public function withRequestTarget($requestTarget);
    public function getMethod();
    /**
     * @param string $method
     */
    public function withMethod($method);
    public function getUri();
    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     */
    public function withUri($uri, $preserveHost = \false);
}
