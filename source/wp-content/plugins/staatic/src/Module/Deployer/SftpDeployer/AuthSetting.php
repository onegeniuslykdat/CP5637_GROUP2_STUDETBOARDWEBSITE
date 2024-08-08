<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class AuthSetting extends AbstractSetting implements ComposedSettingInterface
{
    /**
     * @var HostSetting
     */
    private $host;

    /**
     * @var PortSetting
     */
    private $port;

    /**
     * @var UsernameSetting
     */
    private $username;

    /**
     * @var PasswordSetting
     */
    private $password;

    /**
     * @var SshKeySetting
     */
    private $sshKey;

    /**
     * @var SshKeyPasswordSetting
     */
    private $sshKeyPassword;

    public function __construct(HostSetting $host, PortSetting $port, UsernameSetting $username, PasswordSetting $password, SshKeySetting $sshKey, SshKeyPasswordSetting $sshKeyPassword)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->sshKey = $sshKey;
        $this->sshKeyPassword = $sshKeyPassword;
    }

    public function name(): string
    {
        return 'staatic_sftp_auth';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Authentication', 'staatic');
    }

    public function description(): ?string
    {
        return __('To authenticate via SFTP using an SSH key, leave the password field empty and supply your private SSH key. This method enhances security by using key-based authentication.', 'staatic');
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render();
        if ($this->host->value() && $this->port->value() && $this->username->value() && $this->password->value()) {
            echo '<div
                data-staatic-component="SftpStatus"
                data-host="' . esc_attr($this->host->name()) . '"
                data-port="' . esc_attr($this->port->name()) . '"
                data-username="' . esc_attr($this->username->name()) . '"
                data-password="' . esc_attr($this->password->name()) . '"
                data-ssh-key="' . esc_attr($this->sshKey->name()) . '"
                data-ssh-key-password="' . esc_attr($this->sshKeyPassword->name()) . '"
                style="margin-top: 10px;"
            ></div>';
        }
    }

    public function settings(): array
    {
        return [$this->username, $this->password, $this->sshKey, $this->sshKeyPassword];
    }
}
