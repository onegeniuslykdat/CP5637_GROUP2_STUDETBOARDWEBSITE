<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\Vendor\Psr\Log\LoggerInterface;
use RuntimeException;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;

final class SetupTask implements TaskInterface
{
    /**
     * @var WorkDirectorySetting
     */
    private $workDirectory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(WorkDirectorySetting $workDirectory, LoggerInterface $logger)
    {
        $this->workDirectory = $workDirectory;
        $this->logger = $logger;
    }

    public static function name(): string
    {
        return 'setup';
    }

    public function description(): string
    {
        return __('Setting up', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication): bool
    {
        return \true;
    }

    /**
     * @param Publication $publication
     * @param bool $limitedResources
     */
    public function execute($publication, $limitedResources): bool
    {
        $workDirectory = untrailingslashit($this->workDirectory->value());
        $this->logger->info("Ensuring work directory exists in {$workDirectory}");
        $this->ensureDirectoryExists($workDirectory);
        $resourceDirectory = untrailingslashit($this->workDirectory->value()) . '/resources';
        $this->logger->info("Ensuring resource directory exists in {$resourceDirectory}");
        $this->ensureDirectoryExists($resourceDirectory);
        $this->validateDeploymentMethod($publication);

        return \true;
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }
        if (!mkdir($directory, 0777, \true)) {
            throw new RuntimeException("Unable to create directory in {$directory}");
        }
    }

    private function validateDeploymentMethod(Publication $publication): void
    {
        if (!get_option('staatic_deployment_method')) {
            $this->invalidDeploymentMethod(__('No deployment method has been selected yet', 'staatic'));
        }
        $errors = apply_filters('staatic_deployment_strategy_validate', [], $publication);
        if (count($errors) !== 0) {
            $this->invalidDeploymentMethod(implode(', ', $errors));
        }
        $deployStrategy = apply_filters('staatic_deployment_strategy', null, $publication);
        // In case false is returned, this essentially disables deployment and assumes deployment
        // related tasks are inactive.
        if ($deployStrategy === \false) {
            return;
        }
        if (!$deployStrategy instanceof DeployStrategyInterface) {
            $this->invalidDeploymentMethod(
                __('Deployment method did not register "staatic_deployment_strategy" hook', 'staatic')
            );
        }
        if (method_exists($deployStrategy, 'testConfiguration')) {
            $deployStrategy->testConfiguration();
        }
    }

    private function invalidDeploymentMethod(string $message): void
    {
        throw new RuntimeException(sprintf(
            /* translators: %s: Error message. */
            __('Deployment has not been configured correctly: %s', 'staatic'),
            $message
        ));
    }
}
