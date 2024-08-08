<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface MessageInterface
{
    public function getProtocolVersion();
    /**
     * @param string $version
     */
    public function withProtocolVersion($version);
    public function getHeaders();
    /**
     * @param string $name
     */
    public function hasHeader($name);
    /**
     * @param string $name
     */
    public function getHeader($name);
    /**
     * @param string $name
     */
    public function getHeaderLine($name);
    /**
     * @param string $name
     */
    public function withHeader($name, $value);
    /**
     * @param string $name
     */
    public function withAddedHeader($name, $value);
    /**
     * @param string $name
     */
    public function withoutHeader($name);
    public function getBody();
    /**
     * @param StreamInterface $body
     */
    public function withBody($body);
}
