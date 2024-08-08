<?php

namespace Staatic\Vendor\Symfony\Component\Config;

interface ConfigCacheFactoryInterface
{
    /**
     * @param string $file
     * @param callable $callable
     */
    public function cache($file, $callable): ConfigCacheInterface;
}
