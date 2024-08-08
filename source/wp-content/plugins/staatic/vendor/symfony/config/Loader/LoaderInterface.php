<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

interface LoaderInterface
{
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null);
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null);
    public function getResolver();
    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver($resolver);
}
