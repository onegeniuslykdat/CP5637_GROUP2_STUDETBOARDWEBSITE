<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\DeployStrategy\FilesystemDeployStrategy;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Util\WordpressEnv;

final class FilesystemDeployStrategyFactory
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    public function __construct(ResourceRepositoryInterface $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    public function __invoke(Publication $publication): DeployStrategyInterface
    {
        return new FilesystemDeployStrategy($this->resourceRepository, $this->options($publication));
    }

    private function options(Publication $publication): array
    {
        $targetDirectory = get_option('staatic_filesystem_target_directory');
        $stagingDirectory = trailingslashit(get_option('staatic_work_directory')) . 'staging/';
        $retainPaths = RetainPaths::resolve(get_option('staatic_filesystem_retain_paths') ?: null, $targetDirectory);
        $retainPaths = apply_filters('staatic_filesystem_retain_paths', $retainPaths);
        $options = [
            'targetDirectory' => $targetDirectory,
            'stagingDirectory' => $stagingDirectory,
            'createApacheConfigs' => (bool) get_option('staatic_filesystem_apache_configs'),
            'createNginxConfigs' => (bool) get_option('staatic_filesystem_nginx_configs'),
            'basePath' => $publication->build()->destinationUrl()->getPath(),
            'retainPaths' => $retainPaths,
            'excludePaths' => [get_option('staatic_work_directory'), get_option('staatic_filesystem_target_directory')],
            'copyOnWindows' => \true,
            'htmlAsDirectories' => (bool) apply_filters('staatic_filesystem_html_as_directories', \false)
        ];
        if (get_option('staatic_filesystem_symlink_uploads')) {
            $sourceUploadsDirectory = WordpressEnv::getUploadsPath();
            $targetUploadsDirectory = WordpressEnv::getUploadsUrlPath();
            $options['symlinks'] = [
                $sourceUploadsDirectory => $targetUploadsDirectory
            ];
        }

        return $options;
    }
}
