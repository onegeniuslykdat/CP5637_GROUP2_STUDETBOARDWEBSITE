<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use RuntimeException;
class DirectoryResource implements SelfCheckingResourceInterface
{
    /**
     * @var string
     */
    private $resource;
    /**
     * @var string|null
     */
    private $pattern;
    public function __construct(string $resource, ?string $pattern = null)
    {
        $resolvedResource = realpath($resource) ?: (file_exists($resource) ? $resource : \false);
        $this->pattern = $pattern;
        if (\false === $resolvedResource || !is_dir($resolvedResource)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $resource));
        }
        $this->resource = $resolvedResource;
    }
    public function __toString(): string
    {
        return hash('md5', serialize([$this->resource, $this->pattern]));
    }
    public function getResource(): string
    {
        return $this->resource;
    }
    public function getPattern(): ?string
    {
        return $this->pattern;
    }
    /**
     * @param int $timestamp
     */
    public function isFresh($timestamp): bool
    {
        if (!is_dir($this->resource)) {
            return \false;
        }
        if ($timestamp < filemtime($this->resource)) {
            return \false;
        }
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->resource), RecursiveIteratorIterator::SELF_FIRST) as $file) {
            if ($this->pattern && $file->isFile() && !preg_match($this->pattern, $file->getBasename())) {
                continue;
            }
            if ($file->isDir() && substr_compare($file, '/..', -strlen('/..')) === 0) {
                continue;
            }
            try {
                $fileMTime = $file->getMTime();
            } catch (RuntimeException $exception) {
                continue;
            }
            if ($timestamp < $fileMTime) {
                return \false;
            }
        }
        return \true;
    }
}
