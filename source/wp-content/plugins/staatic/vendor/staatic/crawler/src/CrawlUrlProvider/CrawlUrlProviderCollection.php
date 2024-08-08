<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
final class CrawlUrlProviderCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $providers = [];
    public function __construct(array $providers = [])
    {
        $this->addProviders($providers);
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->providers);
    }
    /**
     * @param mixed[] $providers
     */
    public function addProviders($providers): void
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }
    /**
     * @param CrawlUrlProviderInterface $provider
     */
    public function addProvider($provider): void
    {
        $this->providers[] = $provider;
    }
}
