<?php

namespace Staatic\Crawler\UrlTransformer;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class OfflineUrlTransformer implements UrlTransformerInterface
{
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transform($url, $foundOnUrl = null, $context = []): UrlTransformation
    {
        if (!$url->getPath()) {
            $url = $url->withPath('/');
        } elseif (strncmp($url->getPath(), '//', strlen('//')) === 0) {
            $url = $url->withPath(preg_replace('~^/+~', '/', $url->getPath()));
        }
        $transformedUrl = (new Uri())->withPath($url->getPath())->withQuery($url->getQuery())->withFragment($url->getFragment());
        $effectiveUrl = $foundOnUrl ? UriResolver::relativize($foundOnUrl, $url) : new Uri('');
        $effectiveUrl = $this->addIndexIfNeeded($effectiveUrl);
        return new UrlTransformation($transformedUrl, $effectiveUrl);
    }
    private function addIndexIfNeeded(UriInterface $url): UriInterface
    {
        $path = $url->getPath();
        if ($path === '') {
            return $url;
        }
        if (substr_compare($path, '/', -strlen('/')) === 0) {
            return $url->withPath($path . 'index.html');
        }
        if (strpos(basename($path), '.') === false) {
            return $url->withPath(rtrim($path, '/') . '/index.html');
        }
        return $url;
    }
}
