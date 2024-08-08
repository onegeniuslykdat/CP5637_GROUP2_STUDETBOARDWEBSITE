<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

interface DirectoryAwareLoaderInterface
{
    /**
     * @param string $currentDirectory
     * @return static
     */
    public function forDirectory($currentDirectory);
}
