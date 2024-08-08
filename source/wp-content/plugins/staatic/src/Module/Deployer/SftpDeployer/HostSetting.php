<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class HostSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_sftp_host';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Host', 'staatic');
    }

    public function description(): string
    {
        return __('The SFTP server\'s hostname or IP address.', 'staatic');
    }
}
