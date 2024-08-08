<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Deployment;

use Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule;
use Staatic\WordPress\Setting\AbstractSetting;

final class DeploymentMethodSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_deployment_method';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    protected function template(): string
    {
        return 'select';
    }

    public function label(): string
    {
        return __('Deployment Method', 'staatic');
    }

    public function extendedLabel(): ?string
    {
        return __('Deploy static site to', 'staatic');
    }

    public function description(): ?string
    {
        return __('Choose where and how you want to publish the static version of your site.', 'staatic');
    }

    public function defaultValue()
    {
        return FilesystemDeployerModule::DEPLOYMENT_METHOD_NAME;
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'selectOptions' => $this->selectOptions()
        ], $attributes));
    }

    private function selectOptions(): array
    {
        $deploymentMethods = apply_filters('staatic_deployment_methods', [
            '' => ''
        ]);
        uksort($deploymentMethods, 'strnatcmp');

        return $deploymentMethods;
    }
}
