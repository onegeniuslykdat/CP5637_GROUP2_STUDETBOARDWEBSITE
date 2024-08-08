<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

class DirectoryLoader extends FileLoader
{
    /**
     * @param mixed $file
     * @param string|null $type
     * @return mixed
     */
    public function load($file, $type = null)
    {
        $file = rtrim($file, '/');
        $path = $this->locator->locate($file);
        $this->container->fileExists($path, \false);
        foreach (scandir($path) as $dir) {
            if ('.' !== $dir[0]) {
                if (is_dir($path . '/' . $dir)) {
                    $dir .= '/';
                }
                $this->setCurrentDir($path);
                $this->import($dir, null, \false, $path);
            }
        }
        return null;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        if ('directory' === $type) {
            return \true;
        }
        return null === $type && \is_string($resource) && substr_compare($resource, '/', -strlen('/')) === 0;
    }
}
