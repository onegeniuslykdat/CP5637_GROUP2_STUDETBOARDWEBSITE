<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class TimeoutSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_sftp_timeout';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('Timeout', 'staatic');
    }

    public function description(): string
    {
        return __('The maximum number of seconds to wait for an SFTP command before timing out.', 'staatic');
    }

    public function defaultValue()
    {
        return 10;
    }
}
