<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use SplFileInfo;
use FilesystemIterator;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;

final class ResourceCleanup
{
    /**
     * @var ResultRepository
     */
    private $resultRepository;

    /**
     * @var WorkDirectorySetting
     */
    private $workDirectory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private const CHUNK_SIZE = 50;

    /**
     * @var string
     */
    private $resourceDirectory;

    public function __construct(ResultRepository $resultRepository, WorkDirectorySetting $workDirectory, LoggerInterface $logger)
    {
        $this->resultRepository = $resultRepository;
        $this->workDirectory = $workDirectory;
        $this->logger = $logger;
    }

    public function cleanup(): void
    {
        $this->resourceDirectory = untrailingslashit($this->workDirectory->value()) . '/resources/';
        if (!is_dir($this->resourceDirectory)) {
            return;
        }
        $numRemoved = 0;
        foreach ($this->obsoletePaths() as $path) {
            if (!unlink($path)) {
                $this->logger->warning("Unable to delete obsolete publication resource file in: {$path}");

                continue;
            }
            $numRemoved++;
        }
        $this->logger->info("Cleaned up {$numRemoved} obsolete publication resource files.");
    }

    /** @return iterable<string, string> */
    private function obsoletePaths(): iterable
    {
        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            $this->resourceDirectory,
            $flags
        ), RecursiveIteratorIterator::LEAVES_ONLY);
        $chunks = $this->pathsToChunks($paths);
        foreach ($chunks as $chunk) {
            yield from $this->processChunk($chunk);
        }
    }

    /**
     * @param iterable<string, SplFileInfo> $paths
     * @return iterable<int, array>
     **/
    private function pathsToChunks(iterable $paths): iterable
    {
        $chunk = [];
        $currentChunkSize = 0;
        foreach ($paths as $path => $fileInfo) {
            if (!$fileInfo->isFile() || !$fileInfo->isWritable()) {
                continue;
            }
            $hash = strtr($path, [
                $this->resourceDirectory => '',
                '/' => ''
            ]);
            if (strlen($hash) !== 40) {
                continue;
            }
            $chunk[$path] = $hash;
            $currentChunkSize++;
            if ($currentChunkSize >= self::CHUNK_SIZE) {
                yield $chunk;
                $chunk = [];
                $currentChunkSize = 0;
            }
        }
        if ($currentChunkSize > 0) {
            yield $chunk;
        }
    }

    /** @return iterable<string, string> */
    private function processChunk(array $chunk): iterable
    {
        $knownHashes = $this->resultRepository->getKnownSha1Hashes($chunk);
        foreach ($chunk as $path => $hash) {
            if (!in_array($hash, $knownHashes)) {
                yield $hash => $path;
            }
        }
    }
}
