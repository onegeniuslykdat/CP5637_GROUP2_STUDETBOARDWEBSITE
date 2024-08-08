<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

interface LoaderResolverInterface
{
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return LoaderInterface|false
     */
    public function resolve($resource, $type = null);
}
