<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use Staatic\WordPress\Setting\Build\PreviewUrlSetting;
use Staatic\WordPress\Setting\Build\AdditionalUrlsSetting;
use Staatic\WordPress\Setting\Build\AdditionalPathsSetting;
use Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting;
use Staatic\WordPress\Setting\Build\ExcludeUrlsSetting;
use Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting;
use Staatic\WordPress\Setting\Advanced\LoggingLevelSetting;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;
use Staatic\WordPress\Setting\Advanced\HttpNetworkSetting;
use Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting;
use Staatic\WordPress\Setting\Advanced\SslVerifySetting;
use Staatic\WordPress\Setting\Advanced\CrawlerSetting;
use Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout;
use Staatic\WordPress\Setting\Advanced\UninstallSetting;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Service\PartialRenderer;
use Staatic\WordPress\Service\Settings;
use Staatic\WordPress\SettingGroup\SettingGroup;
use Staatic\WordPress\Setting;

final class RegisterSettings implements ModuleInterface
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
     * @var PartialRenderer
     */
    private $renderer;

    public function __construct(Settings $settings, ServiceLocator $settingLocator, PartialRenderer $renderer)
    {
        $this->settings = $settings;
        $this->settingLocator = $settingLocator;
        $this->renderer = $renderer;
    }

    public function hooks(): void
    {
        add_action('init', [$this, 'registerGroups']);
        add_action('init', [$this, 'registerSettings']);
        // Register uninstall setting last to have it at the bottom of the page.
        add_action('init', [$this, 'registerUninstallSetting'], 100);
        add_action('wp_loaded', [$this->settings, 'registerSettings']);
    }

    public function registerGroups(): void
    {
        $groups = [
            new SettingGroup('staatic-welcome', __('Welcome', 'staatic'), 0, [$this, 'getWelcomeDescription']),
            new SettingGroup('staatic-build', __('Build', 'staatic'), 20),
            new SettingGroup('staatic-deployment', __('Deployment', 'staatic'), 40),
            new SettingGroup('staatic-advanced', __('Advanced', 'staatic'), 100),
            new SettingGroup('staatic-premium', __('Staatic Premium', 'staatic_premium'), 200, [
                $this,
                'getPremiumDescription'
            ])
        ];
        $groups = apply_filters('staatic_setting_groups', $groups);
        foreach ($groups as $group) {
            $this->settings->addGroup($group);
        }
    }

    public function getWelcomeDescription(): string
    {
        return $this->renderer->return('admin/settings/_welcome.php');
    }

    public function getPremiumDescription(): string
    {
        return $this->renderer->return('admin/settings/_premium.php');
    }

    public function registerSettings(): void
    {
        $settings = [
            'staatic-build' => array_filter([
                $this->settingLocator->get(DestinationUrlSetting::class),
                $this->settingLocator->get(PreviewUrlSetting::class),
                $this->settingLocator->get(AdditionalUrlsSetting::class),
                $this->settingLocator->get(AdditionalPathsSetting::class),
                $this->settingLocator->get(AdditionalRedirectsSetting::class),
                $this->settingLocator->get(ExcludeUrlsSetting::class)
            ]),
            'staatic-deployment' => [$this->settingLocator->get(DeploymentMethodSetting::class)],
            'staatic-advanced' => [
                $this->settingLocator->get(LoggingLevelSetting::class),
                $this->settingLocator->get(WorkDirectorySetting::class),
                $this->settingLocator->get(HttpNetworkSetting::class),
                $this->settingLocator->get(HttpAuthenticationSetting::class),
                $this->settingLocator->get(SslVerifySetting::class),
                $this->settingLocator->get(CrawlerSetting::class),
                $this->settingLocator->get(BackgroundProcessTimeout::class)
            ]
        ];
        $settings = apply_filters('staatic_settings', $settings);
        foreach ($settings as $groupName => $settings) {
            foreach ($settings as $setting) {
                $this->settings->addSetting($groupName, $setting);
            }
        }
    }

    public function registerUninstallSetting(): void
    {
        $this->settings->addSetting('staatic-advanced', $this->settingLocator->get(UninstallSetting::class));
    }

    public static function getDefaultPriority(): int
    {
        return 40;
    }
}
