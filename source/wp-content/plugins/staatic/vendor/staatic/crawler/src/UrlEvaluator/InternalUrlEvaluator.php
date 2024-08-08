<?php

namespace Staatic\Crawler\UrlEvaluator;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class InternalUrlEvaluator implements UrlEvaluatorInterface
{
    /**
     * @var UriInterface
     */
    private $baseUrl;
    /**
     * @var bool
     */
    private $ignoreScheme = \true;
    public function __construct(UriInterface $baseUrl, bool $ignoreScheme = \true)
    {
        $this->baseUrl = $baseUrl;
        $this->ignoreScheme = $ignoreScheme;
    }
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool
    {
        if (!$this->ignoreScheme && $this->baseUrl->getScheme() !== $resolvedUrl->getScheme()) {
            return \false;
        }
        if ($this->baseUrl->getAuthority() !== $resolvedUrl->getAuthority()) {
            return \false;
        }
        if ($this->baseUrl->getPath() && $this->baseUrl->getPath() !== '/' && strncmp($resolvedUrl->getPath(), $this->baseUrl->getPath(), strlen($this->baseUrl->getPath())) !== 0) {
            return \false;
        }
        return \true;
    }
}
