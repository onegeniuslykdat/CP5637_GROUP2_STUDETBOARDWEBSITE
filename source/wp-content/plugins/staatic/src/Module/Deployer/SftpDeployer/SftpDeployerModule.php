<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Service\Settings;

final class SftpDeployerModule implements ModuleInterface
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var ServiceLocator
     */
    private $settingLocator;

    /**
     * @var SftpDeployStrategyFactory
     */
    private $deployStrategyFactory;

    public const DEPLOYMENT_METHOD_NAME = 'sftp';

    public function __construct(Settings $settings, ServiceLocator $settingLocator, SftpDeployStrategyFactory $deployStrategyFactory)
    {
        $this->settings = $settings;
        $this->settingLocator = $settingLocator;
        $this->deployStrategyFactory = $deployStrategyFactory;
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
        $deployerSettings = [
            $this->settingLocator->get(SftpSetting::class),
            $this->settingLocator->get(AuthSetting::class),
            $this->settingLocator->get(NetworkSetting::class)
        ];
        foreach ($deployerSettings as $setting) {
            $this->settings->addSetting('staatic-deployment', $setting);
        }
    }

    public function enableDeploymentMethod(): void
    {
        if (!$this->isSelectedDeploymentMethod()) {
            return;
        }
        add_filter('staatic_deployment_strategy', [$this, 'createDeploymentStrategy'], 10, 2);
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
        $deploymentMethods[self::DEPLOYMENT_METHOD_NAME] = __('SFTP Server (beta)', 'staatic');

        return $deploymentMethods;
    }

    /**
     * @param Publication $publication
     */
    public function createDeploymentStrategy($deploymentStrategy, $publication): DeployStrategyInterface
    {
        return ($this->deployStrategyFactory)($publication);
    }
}
