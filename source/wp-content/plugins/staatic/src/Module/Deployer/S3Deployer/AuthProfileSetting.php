<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class AuthProfileSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_auth_profile';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Profile', 'staatic');
    }
}
