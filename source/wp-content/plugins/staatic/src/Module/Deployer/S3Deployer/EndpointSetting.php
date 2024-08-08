<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class EndpointSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_endpoint';
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
        return __('Endpoint', 'staatic');
    }

    public function description(): string
    {
        return __('The name of the S3 provider or a custom endpoint address for S3-compatible services.', 'staatic');
    }

    public function defaultValue()
    {
        return '';
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/s3_endpoint.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }
}
