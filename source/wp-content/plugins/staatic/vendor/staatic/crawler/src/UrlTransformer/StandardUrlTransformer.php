<?php

namespace Staatic\Crawler\UrlTransformer;

use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class StandardUrlTransformer implements UrlTransformerInterface
{
    /**
     * @var UriInterface
     */
    private $baseUrl;
    /**
     * @var UriInterface
     */
    private $destinationUrl;
    /**
     * @var bool
     */
    private $strict = \true;
    public function __construct(UriInterface $baseUrl, UriInterface $destinationUrl, bool $strict = \true)
    {
        $this->baseUrl = $baseUrl;
        $this->destinationUrl = $destinationUrl;
        $this->strict = $strict;
    }
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transform($url, $foundOnUrl = null, $context = []): UrlTransformation
    {
        $transformedUrl = $url;
        $basePath = $this->baseUrl->getPath();
        $path = $url->getPath();
        if ($basePath && $basePath !== '/' && strncmp($path, rtrim($basePath, '/'), strlen(rtrim($basePath, '/'))) === 0) {
            $path = mb_substr($path, mb_strlen(rtrim($basePath, '/')));
            if ($path && $path[0] !== '/') {
                if ($this->strict) {
                    throw new InvalidArgumentException("Untransformable URL supplied: {$url} (base path: {$this->baseUrl})");
                }
                return new UrlTransformation($url);
            }
            $transformedUrl = $transformedUrl->withPath($path);
        }
        if ($url->getScheme() && $url->getScheme() !== $this->destinationUrl->getScheme()) {
            $transformedUrl = $transformedUrl->withScheme($this->destinationUrl->getScheme());
        }
        if ($url->getHost()) {
            if ($url->getHost() !== $this->destinationUrl->getHost()) {
                if ($this->destinationUrl->getHost() === '' && strncmp($path, '//', strlen('//')) === 0) {
                    $transformedUrl = $transformedUrl->withPath(preg_replace('~^/+~', '/', $path));
                }
                $transformedUrl = $transformedUrl->withHost($this->destinationUrl->getHost());
            }
            if ($url->getPort() !== $this->destinationUrl->getPort()) {
                $transformedUrl = $transformedUrl->withPort($this->destinationUrl->getPort());
            }
        }
        if ($this->destinationUrl->getPath() && $this->destinationUrl->getPath() !== '/') {
            $transformedUrl = $transformedUrl->withPath(rtrim($this->destinationUrl->getPath(), '/') . '/' . ltrim($transformedUrl->getPath(), '/'));
        }
        return new UrlTransformation($transformedUrl);
    }
}
