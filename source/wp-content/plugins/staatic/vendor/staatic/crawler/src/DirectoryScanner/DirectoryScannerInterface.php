<?php

namespace Staatic\Crawler\DirectoryScanner;

interface DirectoryScannerInterface
{
    /**
     * @param mixed[] $excludePaths
     */
    public function setExcludePaths($excludePaths): void;
    /**
     * @param string $directory
     * @param bool $recursive
     */
    public function scan($directory, $recursive = \true): iterable;
}
