<?php

namespace Staatic\Framework\ResourceRepository;

use Staatic\Vendor\GuzzleHttp\Psr7\StreamWrapper;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use RuntimeException;
use Staatic\Framework\Resource;
use Staatic\Vendor\Symfony\Component\Filesystem\Filesystem;
final class FilesystemResourceRepository implements ResourceRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var bool
     */
    private $compress;
    /**
     * @var string
     */
    private $targetDirectory;
    public function __construct(string $targetDirectory, bool $compress = \true)
    {
        $this->logger = new NullLogger();
        $this->filesystem = new Filesystem();
        $this->setTargetDirectory($targetDirectory);
        $this->setCompress($compress);
    }
    private function setTargetDirectory(string $targetDirectory): void
    {
        if (!is_dir($targetDirectory)) {
            throw new InvalidArgumentException("Target directory does not exist in {$targetDirectory}");
        }
        $this->targetDirectory = rtrim($targetDirectory, '/');
    }
    private function setCompress(bool $compress)
    {
        if ($compress && !in_array('compress.zlib', stream_get_wrappers())) {
            throw new InvalidArgumentException("Unable to activate compression; zlib stream wrapper is not available");
        }
        $this->compress = $compress;
    }
    /**
     * @param Resource $resource
     */
    public function write($resource): void
    {
        $this->logger->debug("Writing resource with sha1 #{$resource->sha1()}");
        $resourcePath = $this->resourcePath($resource->sha1());
        $resourceStream = StreamWrapper::getResource($resource->content());
        if (!is_dir($dir = dirname($resourcePath))) {
            $this->filesystem->mkdir($dir);
        }
        if (($writeHandle = fopen(($this->compress ? 'compress.zlib://' : '') . $resourcePath, 'w')) === \false) {
            throw new RuntimeException("Unable to open resource file for writing in {$resourcePath}");
        }
        stream_copy_to_stream($resourceStream, $writeHandle);
        fclose($writeHandle);
        $this->logger->debug("Wrote resource with sha1 {$resource->sha1()} ({$resource->size()} bytes)");
    }
    /**
     * @param string $sha1
     */
    public function find($sha1): ?Resource
    {
        $resourcePath = $this->resourcePath($sha1);
        if (!is_readable($resourcePath)) {
            return null;
        }
        $resourceStream = fopen(($this->compress ? 'compress.zlib://' : '') . $resourcePath, 'r');
        if ($resourceStream === \false) {
            throw new RuntimeException("Unable to open resource file for reading in {$resourcePath}");
        }
        return Resource::create($resourceStream);
    }
    /**
     * @param string $sha1
     */
    public function delete($sha1): void
    {
        $resourcePath = $this->resourcePath($sha1);
        if (!is_readable($resourcePath)) {
            clearstatcache();
            if (!is_readable($resourcePath)) {
                throw new RuntimeException("Unable to find resource with sha1 {$sha1}");
            }
        }
        $this->filesystem->remove($resourcePath);
    }
    private function resourcePath(string $sha1): string
    {
        return sprintf('%s/%s/%s', $this->targetDirectory, substr($sha1, 0, 1), substr($sha1, 1));
    }
}
