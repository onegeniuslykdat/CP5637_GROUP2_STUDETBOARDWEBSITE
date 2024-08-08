<?php

namespace Staatic\Framework\PostProcessor\AdditionalRedirectsPostProcessor;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class AdditionalRedirect
{
    /**
     * @var string
     */
    private $origin;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var UriInterface
     */
    private $redirectUrl;
    /**
     * @param string|UriInterface $redirectUrl
     */
    public function __construct(string $origin, $redirectUrl, int $statusCode)
    {
        $this->origin = $origin;
        $this->statusCode = $statusCode;
        $this->redirectUrl = is_string($redirectUrl) ? new Uri($redirectUrl) : $redirectUrl;
    }
    public function origin(): string
    {
        return $this->origin;
    }
    public function redirectUrl(): UriInterface
    {
        return $this->redirectUrl;
    }
    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
