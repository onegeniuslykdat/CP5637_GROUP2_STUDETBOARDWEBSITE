<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticDeployerFactory;
use Staatic\WordPress\Publication\Publication;

final class DeployTask implements RestartableTaskInterface
{
    /**
     * @var StaticDeployerFactory
     */
    private $factory;

    public function __construct(StaticDeployerFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function name(): string
    {
        return 'deploy';
    }

    public function description(): string
    {
        return __('Deploying WordPress site', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication): bool
    {
        if ($publication->metadataByKey('skipDeploy')) {
            return \false;
        }

        return \true;
    }

    /**
     * @param Publication $publication
     * @param bool $limitedResources
     */
    public function execute($publication, $limitedResources): bool
    {
        $staticDeployer = ($this->factory)($publication);
        $deployFinished = $staticDeployer->processResults(
            $publication->deployment(),
            $this->factory->batchSize($limitedResources)
        );

        return $deployFinished;
    }
}
