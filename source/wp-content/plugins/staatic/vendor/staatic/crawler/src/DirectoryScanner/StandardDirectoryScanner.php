<?php

namespace Staatic\Crawler\DirectoryScanner;

use CallbackFilterIterator;
use DirectoryIterator;
use Iterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
final class StandardDirectoryScanner implements DirectoryScannerInterface
{
    /**
     * @var mixed[]
     */
    private $excludePaths;
    public function __construct(array $excludePaths = [])
    {
        $this->setExcludePaths($excludePaths);
    }
    public function excludePaths(): array
    {
        return $this->excludePaths;
    }
    /**
     * @param mixed[] $excludePaths
     */
    public function setExcludePaths($excludePaths): void
    {
        $this->excludePaths = [];
        foreach ($excludePaths as $path) {
            $normalizedPath = $this->normalizePath($path);
            $this->excludePaths[] = $normalizedPath;
            if (($resolvedPath = realpath($normalizedPath)) && $normalizedPath !== $resolvedPath) {
                $this->excludePaths[] = $resolvedPath;
            }
        }
    }
    private function normalizePath(string $path): string
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('\\', '/', $path);
        }
        if (substr($path, 1, 1) === ':') {
            $path = ucfirst($path);
        }
        return rtrim($path, '/\\');
    }
    /**
     * @param string $directory
     * @param bool $recursive
     */
    public function scan($directory, $recursive = \true): iterable
    {
        $iterator = $recursive ? $this->getRecursiveIterator($directory) : $this->getNonRecursiveIterator($directory);
        foreach ($iterator as $fileInfo) {
            yield $fileInfo->getPathname() => $fileInfo->getFileInfo();
        }
    }
    private function getRecursiveIterator(string $directory): Iterator
    {
        $flags = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        return new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($directory, $flags), function (SplFileInfo $fileInfo, string $path) {
            return !$this->shouldExcludePath($path);
        }));
    }
    private function getNonRecursiveIterator(string $directory): Iterator
    {
        return new CallbackFilterIterator(new DirectoryIterator($directory), function (SplFileInfo $fileInfo) {
            return $fileInfo->isFile() && !$this->shouldExcludePath($fileInfo->getPathname());
        });
    }
    private function shouldExcludePath(string $path): bool
    {
        return in_array($this->normalizePath($path), $this->excludePaths);
    }
}
