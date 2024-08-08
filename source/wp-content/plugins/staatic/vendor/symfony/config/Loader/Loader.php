<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

use Staatic\Vendor\Symfony\Component\Config\Exception\LoaderLoadException;
abstract class Loader implements LoaderInterface
{
    protected $resolver;
    protected $env;
    public function __construct(?string $env = null)
    {
        $this->env = $env;
    }
    public function getResolver(): LoaderResolverInterface
    {
        return $this->resolver;
    }
    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function import($resource, $type = null)
    {
        return $this->resolve($resource, $type)->load($resource, $type);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function resolve($resource, $type = null): LoaderInterface
    {
        if ($this->supports($resource, $type)) {
            return $this;
        }
        $loader = (null === $this->resolver) ? \false : $this->resolver->resolve($resource, $type);
        if (\false === $loader) {
            throw new LoaderLoadException($resource, null, 0, null, $type);
        }
        return $loader;
    }
}
