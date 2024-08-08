<?php

namespace Staatic\Vendor\Symfony\Component\Config;

class ResourceCheckerConfigCacheFactory implements ConfigCacheFactoryInterface
{
    /**
     * @var iterable
     */
    private $resourceCheckers = [];
    public function __construct(iterable $resourceCheckers = [])
    {
        $this->resourceCheckers = $resourceCheckers;
    }
    /**
     * @param string $file
     * @param callable $callable
     */
    public function cache($file, $callable): ConfigCacheInterface
    {
        $cache = new ResourceCheckerConfigCache($file, $this->resourceCheckers);
        if (!$cache->isFresh()) {
            $callable($cache);
        }
        return $cache;
    }
}
