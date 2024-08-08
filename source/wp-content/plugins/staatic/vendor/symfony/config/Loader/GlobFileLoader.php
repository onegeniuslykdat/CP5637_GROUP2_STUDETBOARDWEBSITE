<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

class GlobFileLoader extends FileLoader
{
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        return $this->import($resource);
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
