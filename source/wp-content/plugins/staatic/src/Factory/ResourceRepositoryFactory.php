<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Framework\ResourceRepository\FilesystemResourceRepository;
use Staatic\Framework\ResourceRepository\InMemoryResourceRepository;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;

final class ResourceRepositoryFactory
{
    /**
     * @var WorkDirectorySetting
     */
    private $workDirectory;

    public function __construct(WorkDirectorySetting $workDirectory)
    {
        $this->workDirectory = $workDirectory;
    }

    public function __invoke(): ResourceRepositoryInterface
    {
        $resourceDirectory = untrailingslashit($this->workDirectory->value()) . '/resources';
        if (!is_dir($resourceDirectory)) {
            if (!mkdir($resourceDirectory, 0777, \true)) {
                return new InMemoryResourceRepository();
            }
        }
        $compress = in_array('compress.zlib', stream_get_wrappers());
        $compress = (bool) apply_filters('staatic_compress_resources', $compress);

        return new FilesystemResourceRepository($resourceDirectory, $compress);
    }
}
