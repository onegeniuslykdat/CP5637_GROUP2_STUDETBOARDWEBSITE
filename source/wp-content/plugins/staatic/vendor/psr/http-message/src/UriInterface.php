<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface UriInterface
{
    public function getScheme();
    public function getAuthority();
    public function getUserInfo();
    public function getHost();
    public function getPort();
    public function getPath();
    public function getQuery();
    public function getFragment();
    /**
     * @param string $scheme
     */
    public function withScheme($scheme);
    /**
     * @param string $user
     * @param string|null $password
     */
    public function withUserInfo($user, $password = null);
    /**
     * @param string $host
     */
    public function withHost($host);
    /**
     * @param int|null $port
     */
    public function withPort($port);
    /**
     * @param string $path
     */
    public function withPath($path);
    /**
     * @param string $query
     */
    public function withQuery($query);
    /**
     * @param string $fragment
     */
    public function withFragment($fragment);
    public function __toString();
}
