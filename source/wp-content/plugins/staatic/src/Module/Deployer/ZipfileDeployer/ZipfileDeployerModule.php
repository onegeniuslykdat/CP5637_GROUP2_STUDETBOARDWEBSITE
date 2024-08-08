<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\ZipfileDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Task\DeployTask;
use Staatic\WordPress\Publication\Task\FinishDeploymentTask;
use Staatic\WordPress\Publication\Task\InitiateDeploymentTask;
use Staatic\WordPress\Publication\Task\TaskCollection;
use Staatic\WordPress\Service\Settings;

final class ZipfileDeployerModule implements ModuleInterface
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var ServiceLocator
     */
    private $settingLocator;

    public const DEPLOYMENT_METHOD_NAME = 'zipfile';

    public function __construct(Settings $settings, ServiceLocator $settingLocator)
    {
        $this->settings = $settings;
        $this->settingLocator = $settingLocator;
    }

    public function hooks(): void
    {
        add_action('init', [$this, 'registerSettings']);
        add_action('wp_loaded', [$this, 'enableDeploymentMethod'], 20);
        if (!is_admin()) {
            return;
        }
        add_filter('staatic_deployment_methods', [$this, 'registerDeploymentMethod']);
    }

    public function registerSettings(): void
    {
        $deployerSettings = [$this->settingLocator->get(ZipfileSetting::class)];
        foreach ($deployerSettings as $setting) {
            $this->settings->addSetting('staatic-deployment', $setting);
        }
    }

    public function enableDeploymentMethod(): void
    {
        if (!$this->isSelectedDeploymentMethod()) {
            return;
        }
        add_filter('staatic_publication_tasks', [$this, 'disableDeploymentTasks']);
        add_filter('staatic_deployment_strategy', '__return_false');
    }

    private function isSelectedDeploymentMethod(): bool
    {
        return get_option('staatic_deployment_method') === self::DEPLOYMENT_METHOD_NAME;
    }

    /**
     * @param mixed[] $deploymentMethods
     */
    public function registerDeploymentMethod($deploymentMethods): array
    {
        $deploymentMethods[self::DEPLOYMENT_METHOD_NAME] = __('Zipfile', 'staatic');

        return $deploymentMethods;
    }

    /**
     * @param TaskCollection $tasks
     */
    public function disableDeploymentTasks($tasks): TaskCollection
    {
        return $tasks->forget([InitiateDeploymentTask::name(), DeployTask::name(), FinishDeploymentTask::name()]);
    }
}
