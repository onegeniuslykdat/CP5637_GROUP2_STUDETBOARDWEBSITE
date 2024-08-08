<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class TargetDirectorySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_sftp_target_directory';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Target Directory', 'staatic');
    }

    public function description(): string
    {
        return sprintf(
            /* translators: %s: Example prefix. */
            __('The path to the directory on the SFTP server where the static version of your site is deployed.<br>Example: <code>%s</code>.', 'staatic'),
            '/some/path/'
        );
    }
}
