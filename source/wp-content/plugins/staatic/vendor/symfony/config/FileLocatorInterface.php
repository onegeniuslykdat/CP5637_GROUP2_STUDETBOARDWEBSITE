<?php

namespace Staatic\Vendor\Symfony\Component\Config;

use Staatic\Vendor\Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
interface FileLocatorInterface
{
    /**
     * @param string $name
     * @param string|null $currentPath
     * @param bool $first
     */
    public function locate($name, $currentPath = null, $first = \true);
}
