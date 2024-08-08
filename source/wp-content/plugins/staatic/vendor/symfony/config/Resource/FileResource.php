<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use InvalidArgumentException;
class FileResource implements SelfCheckingResourceInterface
{
    /**
     * @var string
     */
    private $resource;
    public function __construct(string $resource)
    {
        $resolvedResource = realpath($resource) ?: (file_exists($resource) ? $resource : \false);
        if (\false === $resolvedResource) {
            throw new InvalidArgumentException(sprintf('The file "%s" does not exist.', $resource));
        }
        $this->resource = $resolvedResource;
    }
    public function __toString(): string
    {
        return $this->resource;
    }
    public function getResource(): string
    {
        return $this->resource;
    }
    /**
     * @param int $timestamp
     */
    public function isFresh($timestamp): bool
    {
        return \false !== ($filemtime = @filemtime($this->resource)) && $filemtime <= $timestamp;
    }
}
