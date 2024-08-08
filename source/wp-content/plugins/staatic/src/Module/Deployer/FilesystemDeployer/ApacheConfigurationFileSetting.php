<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class ApacheConfigurationFileSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_filesystem_apache_configs';
    }

    public function type(): string
    {
        return self::TYPE_BOOLEAN;
    }

    public function label(): string
    {
        return __('Generate Apache Configuration', 'staatic');
    }

    public function extendedLabel(): ?string
    {
        return __('Generate .htaccess file providing actual HTTP redirects, HTTP status overrides, etc.', 'staatic');
    }

    public function description(): ?string
    {
        return __('Enable this option if you\'re on an Apache webserver with AllowOverride enabled.', 'staatic');
    }
}
