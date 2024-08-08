<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\NetlifyDeployer;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\Framework\ConfigGenerator\NetlifyConfigGenerator;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\PostProcessor\ConfigGeneratorPostProcessor;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Service\Settings;

final class NetlifyDeployerModule implements ModuleInterface
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
     * @var NetlifyDeployStrategyFactory
     */
    private $deployStrategyFactory;

    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var UrlTransformerFactory
     */
    private $urlTransformerFactory;

    public const DEPLOYMENT_METHOD_NAME = 'netlify';

    public function __construct(Settings $settings, ServiceLocator $settingLocator, NetlifyDeployStrategyFactory $deployStrategyFactory, ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, UrlTransformerFactory $urlTransformerFactory)
    {
        $this->settings = $settings;
        $this->settingLocator = $settingLocator;
        $this->deployStrategyFactory = $deployStrategyFactory;
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->urlTransformerFactory = $urlTransformerFactory;
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
            $this->settingLocator->get(AuthSetting::class),
            $this->settingLocator->get(SiteIdSetting::class)
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
        add_filter('staatic_post_processors', [$this, 'overridePostProcessors'], 10, 2);
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
        $deploymentMethods[self::DEPLOYMENT_METHOD_NAME] = __('Netlify', 'staatic');

        return $deploymentMethods;
    }

    /**
     * @param mixed[] $postProcessors
     * @param Publication $publication
     */
    public function overridePostProcessors($postProcessors, $publication): array
    {
        $netlifyExtraConfig = apply_filters('staatic_netlify_config_extra', '', $publication);
        $postProcessors[] = new ConfigGeneratorPostProcessor(
            $this->resultRepository,
            $this->resourceRepository,
            new NetlifyConfigGenerator(
            $this->notFoundPath(
            $publication
        ),
            (string) $netlifyExtraConfig
        ),
            $this->maybeConfigFilterCallback(
            'staatic_netlify_config_file'
        )
        );

        return $postProcessors;
    }

    private function notFoundPath(Publication $publication): string
    {
        $urlTransformer = ($this->urlTransformerFactory)($publication->build()->entryUrl(), $publication->build()->destinationUrl());
        $notFoundPath = get_option('staatic_page_not_found_path');

        return $urlTransformer->transform(new Uri($notFoundPath))->transformedUrl()->getPath();
    }

    private function maybeConfigFilterCallback(string $hookName)
    {
        if (!has_filter($hookName)) {
            return null;
        }

        return function ($content, $path) use ($hookName) {
            return apply_filters($hookName, $content, $path);
        };
    }

    /**
     * @param Publication $publication
     */
    public function createDeploymentStrategy($deploymentStrategy, $publication): DeployStrategyInterface
    {
        return ($this->deployStrategyFactory)($publication);
    }
}
