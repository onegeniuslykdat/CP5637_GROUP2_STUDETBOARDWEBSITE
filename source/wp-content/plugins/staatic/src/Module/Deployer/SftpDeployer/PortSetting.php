<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class PortSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_sftp_port';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('Port', 'staatic');
    }

    public function description(): string
    {
        return __('The SFTP server\'s port number.', 'staatic');
    }

    public function defaultValue()
    {
        return 22;
    }
}
