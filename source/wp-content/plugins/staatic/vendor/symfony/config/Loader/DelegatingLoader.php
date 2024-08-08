<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

use Staatic\Vendor\Symfony\Component\Config\Exception\LoaderLoadException;
class DelegatingLoader extends Loader
{
    public function __construct(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        if (\false === $loader = $this->resolver->resolve($resource, $type)) {
            throw new LoaderLoadException($resource, null, 0, null, $type);
        }
        return $loader->load($resource, $type);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return \false !== $this->resolver->resolve($resource, $type);
    }
}
