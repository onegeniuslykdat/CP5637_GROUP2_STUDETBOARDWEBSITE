<?php

namespace Staatic\Crawler\KnownUrlsContainer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
class InMemoryKnownUrlsContainer implements KnownUrlsContainerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $urls = [];
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    public function clear(): void
    {
        $this->urls = [];
    }
    /**
     * @param UriInterface $url
     */
    public function add($url): void
    {
        if ($this->isKnown($url)) {
            throw new RuntimeException("Url '{$url}' is already known");
        }
        $this->logger->debug("Adding url '{$url}' to container");
        $this->urls[(string) $url] = \true;
    }
    /**
     * @param UriInterface $url
     */
    public function isKnown($url): bool
    {
        return isset($this->urls[(string) $url]);
    }
    public function count(): int
    {
        return count($this->urls);
    }
}
