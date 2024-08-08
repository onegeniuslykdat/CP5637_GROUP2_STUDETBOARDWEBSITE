<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

class GlobFileLoader extends FileLoader
{
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        foreach ($this->glob($resource, \false, $globResource) as $path => $info) {
            $this->import($path);
        }
        $this->container->addResource($globResource);
        return null;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return 'glob' === $type;
    }
}
