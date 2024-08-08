<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\StoresEncryptedInterface;

final class SshKeyPasswordSetting extends AbstractSetting implements StoresEncryptedInterface
{
    public function name(): string
    {
        return 'staatic_sftp_ssh_key_password';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    protected function template(): string
    {
        return 'password';
    }

    public function label(): string
    {
        return __('SSH Key Password', 'staatic');
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'disableAutocomplete' => \true
        ], $attributes));
    }
}
