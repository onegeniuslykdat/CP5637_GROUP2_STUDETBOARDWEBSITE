<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class UsernameSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_sftp_username';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Username', 'staatic');
    }
}
