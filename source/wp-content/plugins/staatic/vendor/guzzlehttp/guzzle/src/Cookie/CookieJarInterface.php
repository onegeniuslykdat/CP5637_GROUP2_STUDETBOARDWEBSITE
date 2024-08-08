<?php

namespace Staatic\Vendor\GuzzleHttp\Cookie;

use Countable;
use IteratorAggregate;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
interface CookieJarInterface extends Countable, IteratorAggregate
{
    /**
     * @param RequestInterface $request
     */
    public function withCookieHeader($request): RequestInterface;
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function extractCookies($request, $response): void;
    /**
     * @param SetCookie $cookie
     */
    public function setCookie($cookie): bool;
    /**
     * @param string|null $domain
     * @param string|null $path
     * @param string|null $name
     */
    public function clear($domain = null, $path = null, $name = null): void;
    public function clearSessionCookies(): void;
    public function toArray(): array;
}
