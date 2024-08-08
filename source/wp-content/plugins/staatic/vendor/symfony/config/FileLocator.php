<?php

namespace Staatic\Vendor\Symfony\Component\Config;

use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
class FileLocator implements FileLocatorInterface
{
    protected $paths;
    /**
     * @param string|mixed[] $paths
     */
    public function __construct($paths = [])
    {
        $this->paths = (array) $paths;
    }
    /**
     * @param string $name
     * @param string|null $currentPath
     * @param bool $first
     */
    public function locate($name, $currentPath = null, $first = \true)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('An empty file name is not valid to be located.');
        }
        if ($this->isAbsolutePath($name)) {
            if (!file_exists($name)) {
                throw new FileLocatorFileNotFoundException(sprintf('The file "%s" does not exist.', $name), 0, null, [$name]);
            }
            return $name;
        }
        $paths = $this->paths;
        if (null !== $currentPath) {
            array_unshift($paths, $currentPath);
        }
        $paths = array_unique($paths);
        $filepaths = $notfound = [];
        foreach ($paths as $path) {
            if (@file_exists($file = $path . \DIRECTORY_SEPARATOR . $name)) {
                if (\true === $first) {
                    return $file;
                }
                $filepaths[] = $file;
            } else {
                $notfound[] = $file;
            }
        }
        if (!$filepaths) {
            throw new FileLocatorFileNotFoundException(sprintf('The file "%s" does not exist (in: "%s").', $name, implode('", "', $paths)), 0, null, $notfound);
        }
        return $filepaths;
    }
    private function isAbsolutePath(string $file): bool
    {
        if ('/' === $file[0] || '\\' === $file[0] || \strlen($file) > 3 && ctype_alpha($file[0]) && ':' === $file[1] && ('\\' === $file[2] || '/' === $file[2]) || null !== parse_url($file, \PHP_URL_SCHEME)) {
            return \true;
        }
        return \false;
    }
}
