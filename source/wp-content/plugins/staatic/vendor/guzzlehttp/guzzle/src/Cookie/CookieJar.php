<?php

namespace Staatic\Vendor\GuzzleHttp\Cookie;

use RuntimeException;
use ArrayIterator;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
class CookieJar implements CookieJarInterface
{
    private $cookies = [];
    private $strictMode;
    public function __construct(bool $strictMode = \false, array $cookieArray = [])
    {
        $this->strictMode = $strictMode;
        foreach ($cookieArray as $cookie) {
            if (!$cookie instanceof SetCookie) {
                $cookie = new SetCookie($cookie);
            }
            $this->setCookie($cookie);
        }
    }
    /**
     * @param mixed[] $cookies
     * @param string $domain
     */
    public static function fromArray($cookies, $domain): self
    {
        $cookieJar = new self();
        foreach ($cookies as $name => $value) {
            $cookieJar->setCookie(new SetCookie(['Domain' => $domain, 'Name' => $name, 'Value' => $value, 'Discard' => \true]));
        }
        return $cookieJar;
    }
    /**
     * @param SetCookie $cookie
     * @param bool $allowSessionCookies
     */
    public static function shouldPersist($cookie, $allowSessionCookies = \false): bool
    {
        if ($cookie->getExpires() || $allowSessionCookies) {
            if (!$cookie->getDiscard()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param string $name
     */
    public function getCookieByName($name): ?SetCookie
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() !== null && \strcasecmp($cookie->getName(), $name) === 0) {
                return $cookie;
            }
        }
        return null;
    }
    public function toArray(): array
    {
        return \array_map(static function (SetCookie $cookie): array {
            return $cookie->toArray();
        }, $this->getIterator()->getArrayCopy());
    }
    /**
     * @param string|null $domain
     * @param string|null $path
     * @param string|null $name
     */
    public function clear($domain = null, $path = null, $name = null): void
    {
        if (!$domain) {
            $this->cookies = [];
            return;
        } elseif (!$path) {
            $this->cookies = \array_filter($this->cookies, static function (SetCookie $cookie) use ($domain): bool {
                return !$cookie->matchesDomain($domain);
            });
        } elseif (!$name) {
            $this->cookies = \array_filter($this->cookies, static function (SetCookie $cookie) use ($path, $domain): bool {
                return !($cookie->matchesPath($path) && $cookie->matchesDomain($domain));
            });
        } else {
            $this->cookies = \array_filter($this->cookies, static function (SetCookie $cookie) use ($path, $domain, $name) {
                return !($cookie->getName() == $name && $cookie->matchesPath($path) && $cookie->matchesDomain($domain));
            });
        }
    }
    public function clearSessionCookies(): void
    {
        $this->cookies = \array_filter($this->cookies, static function (SetCookie $cookie): bool {
            return !$cookie->getDiscard() && $cookie->getExpires();
        });
    }
    /**
     * @param SetCookie $cookie
     */
    public function setCookie($cookie): bool
    {
        $name = $cookie->getName();
        if (!$name && $name !== '0') {
            return \false;
        }
        $result = $cookie->validate();
        if ($result !== \true) {
            if ($this->strictMode) {
                throw new RuntimeException('Invalid cookie: ' . $result);
            }
            $this->removeCookieIfEmpty($cookie);
            return \false;
        }
        foreach ($this->cookies as $i => $c) {
            if ($c->getPath() != $cookie->getPath() || $c->getDomain() != $cookie->getDomain() || $c->getName() != $cookie->getName()) {
                continue;
            }
            if (!$cookie->getDiscard() && $c->getDiscard()) {
                unset($this->cookies[$i]);
                continue;
            }
            if ($cookie->getExpires() > $c->getExpires()) {
                unset($this->cookies[$i]);
                continue;
            }
            if ($cookie->getValue() !== $c->getValue()) {
                unset($this->cookies[$i]);
                continue;
            }
            return \false;
        }
        $this->cookies[] = $cookie;
        return \true;
    }
    public function count(): int
    {
        return \count($this->cookies);
    }
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(\array_values($this->cookies));
    }
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function extractCookies($request, $response): void
    {
        if ($cookieHeader = $response->getHeader('Set-Cookie')) {
            foreach ($cookieHeader as $cookie) {
                $sc = SetCookie::fromString($cookie);
                if (!$sc->getDomain()) {
                    $sc->setDomain($request->getUri()->getHost());
                }
                if (0 !== \strpos($sc->getPath(), '/')) {
                    $sc->setPath($this->getCookiePathFromRequest($request));
                }
                if (!$sc->matchesDomain($request->getUri()->getHost())) {
                    continue;
                }
                $this->setCookie($sc);
            }
        }
    }
    private function getCookiePathFromRequest(RequestInterface $request): string
    {
        $uriPath = $request->getUri()->getPath();
        if ('' === $uriPath) {
            return '/';
        }
        if (0 !== \strpos($uriPath, '/')) {
            return '/';
        }
        if ('/' === $uriPath) {
            return '/';
        }
        $lastSlashPos = \strrpos($uriPath, '/');
        if (0 === $lastSlashPos || \false === $lastSlashPos) {
            return '/';
        }
        return \substr($uriPath, 0, $lastSlashPos);
    }
    /**
     * @param RequestInterface $request
     */
    public function withCookieHeader($request): RequestInterface
    {
        $values = [];
        $uri = $request->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $path = $uri->getPath() ?: '/';
        foreach ($this->cookies as $cookie) {
            if ($cookie->matchesPath($path) && $cookie->matchesDomain($host) && !$cookie->isExpired() && (!$cookie->getSecure() || $scheme === 'https')) {
                $values[] = $cookie->getName() . '=' . $cookie->getValue();
            }
        }
        return $values ? $request->withHeader('Cookie', \implode('; ', $values)) : $request;
    }
    private function removeCookieIfEmpty(SetCookie $cookie): void
    {
        $cookieValue = $cookie->getValue();
        if ($cookieValue === null || $cookieValue === '') {
            $this->clear($cookie->getDomain(), $cookie->getPath(), $cookie->getName());
        }
    }
}
